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

class Facebook extends Core
{
    public $id = 'facebook';

    public $dbIdField = 'fb_id';

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
            'scope'         => 'email,user_birthday' // vk - no scope; fb has scope
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
                //  trace($this->user);
            }

        }

        parent::__token();
    }

    public function setUser(array $user)
    {
        $this->user = new User();
        $this->user->birthday = $user['birthday'];
        $this->user->email = $user['email'];
        $this->user->gender = $user['gender'];
        $this->user->locale = $user['locale'];
        $this->user->name = $user['first_name'];
        $this->user->surname = $user['last_name'];
        $this->user->socialID = $user['id'];

        parent::setUser($user);
    }

}