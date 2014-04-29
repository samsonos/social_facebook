<?php
/**
 * Created by Svyatoslav Svitlychnyi <svitlychnyi@samsonos.com>
 * on 11.02.14 at 11:35
 */

namespace samson\social;

/**
 *
 * @author Svyatoslav Svitlychnyi <svitlychnyi@samsonos.com>
 * @copyright 2013 SamsonOS
 * @version
 */

class Facebook extends \samson\social\Network
{
    public $id = 'facebook';

    public $dbIdField = 'fb_id';

    public $baseURL = 'https://graph.facebook.com/';

    public $socialURL = 'https://www.facebook.com/dialog/oauth';

    public $tokenURL = 'https://graph.facebook.com/oauth/access_token';

    public $userURL = 'https://graph.facebook.com/me';

    public function __HANDLER()
    {
        parent::__HANDLER();

        // Send http get request to retrieve VK code
        $this->redirect($this->socialURL, array(
            'client_id'     => $this->appCode,
            'redirect_uri'  => $this->returnURL(),
            'response_type' => 'code',
            'scope'         => 'email,user_birthday'
        ));
    }

    public function __token()
    {
        $code = & $_GET['code'];
        if (isset($code)) {

            // Send http get request to retrieve VK code
            $token = $this->post($this->tokenURL, array(
                'client_id' => $this->appCode,
                'client_secret' => $this->appSecret,
                'code' => $code,
                'redirect_uri' => $this->returnURL(),

            ));

            parse_str($token, $token);

            // take user's information using access token
            if (isset($token)) {
                $userInfo = $this->get($this->userURL, array(
                    'access_token' => $token['access_token']
                ));

                $this->setUser($userInfo);
            }

        }

        parent::__token();
    }

    protected function setUser(array $userData, & $user = null)
    {
        $user = new User();
        $user->birthday = $userData['birthday'];
        $user->email = $userData['email'];
        $user->gender = $userData['gender'];
        $user->locale = $userData['locale'];
        $user->name = $userData['first_name'];
        $user->surname = $userData['last_name'];
        $user->socialID = $userData['id'];
        $user->photo = $this->baseURL.$userData['id'].'/picture';

        parent::setUser($userData, $user);
    }

}