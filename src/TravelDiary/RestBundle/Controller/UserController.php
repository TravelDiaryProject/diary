<?php

namespace TravelDiary\RestBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class UserController extends FOSRestController
{
    /**
     * @Rest\Post("/register")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function registerAction(Request $request)
    {
        $userManager = $this->get('fos_user.user_manager');
        $data = $request->request->all();

        $user = $userManager->createUser();
        $user->setUsername($data['_email']);
        $user->setEmail($data['_email']);
        // ...
        $user->setPlainPassword($data['_password']);
        $user->setEnabled(true);

        $errorMessage = '';

        try {
            $this->validate($user);
            $userManager->updateUser($user);
        } catch (\InvalidArgumentException $e) {
            $errorMessage = $e->getMessage();
        } catch (\Exception $e) {
            $errorMessage = 'Something went wrong';
        }

        if ('' !== $errorMessage) {
            $response = [
                'error' => $errorMessage
            ];

            return new JsonResponse($response, 400);
        }


        return $this->generateToken($user, 201);
    }

    protected function generateToken($user, $statusCode = 200)
    {
        // Generate the token
        $token = $this->get('lexik_jwt_authentication.jwt_manager')->create($user);

        $response = array(
            'token' => $token,
        );

        return new JsonResponse($response, $statusCode); // Return a 201 Created with the JWT.
    }

    /**
     * @param UserInterface $user
     *
     * @throws \InvalidArgumentException
     */
    private function validate(UserInterface $user)
    {
        $existingUser = $this->get('fos_user.user_manager')->findUserByEmail($user->getEmail());

        if ($existingUser) {
            throw new \InvalidArgumentException(sprintf('User with email %s already exists', $user->getEmail()));
        }

        $validator = $this->get('validator');

        /** @var ConstraintViolationListInterface $violations */
        $violations = $validator->validate($user);

        if (count($violations)) {
            throw new \InvalidArgumentException($violations->get(0)->getMessage());
        }
    }
}
