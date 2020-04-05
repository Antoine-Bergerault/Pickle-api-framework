<?php
namespace Pickle;
use Pickle\Engine\App;

class UserController{

    public function register(){//register a new user

        App::middleware('csrf')->verifyToken();

        $errors = [];//initialize the errors array
        if ($_POST['pass'] != $_POST['pass_verify']) {//check if datas are good
            $errors[] = 'Les mots de passes ne correspondent pas';//if not add an error
        }

        if (!empty($errors)) {//if there is/are error(s), go to the register page
            return view('user/register', compact('errors'));
        }

        $name = trim(transliterator_transliterate('Any-Latin; Latin-ASCII; [\u0080-\u7fff] remove', $_POST['name']));

        $Model = new UserModel();

        $users = $Model->first()->where([
            'name' => '= ?'
        ])->args([$name])->run();
        
        if(!empty($users)){
            $errors[] = "Nom d'utilisateur déjà utilisé";
            return view('user/register', compact('errors'));
        }

        $Model->create([
            "name" => $name,
            'email' => $_POST['email'],
            'pass' => $_POST['pass']
        ]);

        if(App::isset_session('contributor_id')){
            $i = $Model->lastinsertid();
            $ContributorModel = new ContributorModel();
            $ContributorModel->addAccount(App::get('contributor_id'), $i);
        }

        App::save('flash', ['Votre compte a été créé avec succès']);

        include ROOT.'/core/App/Email.php';
        \Pickle\Engine\Email::send('Bienvenue sur Coursonline !', ['name' => $usr->name, 'link' => url('/validate')], 'inscription', $usr->email);

        redirect(url('/'));

    }

    public function login(){//login

        App::middleware('csrf')->verifyToken();

        $errors = [];//initialize the errors array

        $User = new UserModel();//initialize the User model

        $arg = [
            'name' => $_POST['name'],
            'pass' => $_POST['pass']
        ];

        $usr = $User->get_user($arg);//try to find a user with the same informations

        if ($usr == false) {//if not
            $arg = [
                'email' => $_POST['name'],
                'pass' => $_POST['pass']
            ];
            $usr = $User->get_user($arg);//try the same with the email instead of name
            if ($usr == false) {//if doesn't work
                $errors[] = "Nom d'utilisateur ou mot de passe non valide";//add an error
            }
        }

        if (!empty($errors)) {//if there is/are error(s), return to the login page
            return view('user/login', compact('errors'));
        }
        //else
        $remember = isset($_POST['remember']);
        App::connect($usr, $remember);//connect the website

        App::save('flash', ['Vous êtes connecté']);

        redirect(url('/profile'));

    }

    public function logout(){
        App::logout();
        redirect(url('/'));
    }
    
    public function connect(){
        $code = $_GET['code'] ?? false;
        if($code === false){
            return false;
        }
        require_once ROOT.'/vendor/autoload.php';
        $client = new \GuzzleHttp\Client([
            'timeout' => 2.0
        ]);
        try{
            $response = $client->request('GET', 'https://accounts.google.com/.well-known/openid-configuration');
            $discovery_json = json_decode((string) $response->getBody());
            $token_endpoint = $discovery_json->token_endpoint;
            $userinfo_endpoint = $discovery_json->userinfo_endpoint;
            $response = $client->request('POST', $token_endpoint, [
                'form_params' => [
                    'code' => $code,
                    'client_id' => \Pickle\Tools\Config::$google_id,
                    'client_secret' => \Pickle\Tools\Config::$google_secret,
                    'redirect_uri' => url('/connect'),
                    'grant_type' => 'authorization_code'
                ]
            ]);
            $access_token = json_decode($response->getBody())->access_token;
            $response = $client->request('GET', $userinfo_endpoint, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $access_token
                ]
            ]);
            $response = json_decode($response->getBody());
            if($response->email_verified === true){
                $email = $response->email;
                $name = $response->given_name;
                $UserModel = new UserModel();//initialize the User model
                $arg = [
                    'email' => $email
                ];
                $usr = $UserModel->get_user($arg);
                if($usr == false){
                    $nname = $name;
                    $usr = true;
                    while($usr !== false){
                        $name = $nname;
                        $usr = $UserModel->get_user([
                            'name' => trim($name)
                        ]);
                        $nname = $name . \str_random(4);
                    }
                    $UserModel->create([
                        "name" => $name,
                        'email' => $email,
                        'oauth' => 'google'
                    ]);
                    $usr = $UserModel->get_user([
                        'name' => $name,
                        'email' => $email
                    ]);
                    App::connect($usr, true);
                    include ROOT.'/core/App/Email.php';
                    \Pickle\Engine\Email::send('Bienvenue sur Coursonline !', ['name' => $usr->name, 'link' => url('/validate')], 'inscription', $usr->email);
                    
                    if(App::isset_session('contributor_id')){
                        $i = $UserModel->lastinsertid();
                        $ContributorModel = new ContributorModel();
                        $ContributorModel->addAccount(App::get('contributor_id'), $i);
                    }
                    
                    redirect(url('/'));
                }else{
                    App::connect($usr, true);
                    include ROOT.'/core/App/Email.php';
                    \Pickle\Engine\Email::send('Bienvenue sur Coursonline !', ['name' => $usr->name, 'link' => url('/validate')], 'inscription', $usr->email);
                    redirect(url('/'));
                }
            }
        }catch(\GuzzleHttp\Exception\ClientException $exception){
            dd($exception->getMessage());
        }        
    }    
}