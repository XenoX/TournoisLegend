<?php

namespace UserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use TournamentBundle\Entity\Participant;
use TournamentBundle\Entity\RankingLevel;
use TournamentBundle\Entity\RankingParticipant;
use UserBundle\Entity\Notification;
use UserBundle\Entity\NotificationTemplate;
use UserBundle\Entity\TeamMember;
use UserBundle\Entity\User;
use UserBundle\Form\EmailOrUsernameType;
use UserBundle\Form\LoginType;
use UserBundle\Form\PasswordForgotType;
use UserBundle\Form\PasswordSettingsType;
use UserBundle\Form\PersonnalInformationsSettingsType;
use UserBundle\Form\RegistrationType;
use UserBundle\Form\RegistrationConfirmationType;
use UserBundle\Form\SocialSettingsType;

/**
 * @Route("/user")
 */
class UserController extends Controller
{
    /**
     * @Route("/profile/{user}-{slug}", defaults={"slug": null})
     * @Template()
     */
    public function profileAction(User $user)
    {
        if (false === $user->isClean()) {
            return $this->redirectToRoute('app_app_index');
        }

        $em = $this->getDoctrine()->getManager();
        $teamsMember = $em->getRepository(TeamMember::class)->findBy(['user' => $user, 'requested' => false]);
        foreach ($teamsMember as $key => $teamMember) {
            if ($teamMember->getTeam()->isClean()) {
                continue;
            }

            unset($teamsMember[$key]);
        }

        $rankingParticipants = $em->getRepository(RankingParticipant::class)->getRankingParticipantsForUser($user);
        $lastTournaments = $em->getRepository(Participant::class)->findProfileLastTournaments($user, null, 5);
        $upcomingTournaments = $em->getRepository(Participant::class)->findProfileNextTournaments($user, null, 5);

        $rankingLevels = $em->getRepository(RankingLevel::class)->findBy(['activated' => true], ['eloMax' => 'ASC']);

        return [
            'user' => $user,
            'teamsMember' => $teamsMember,
            'rankingParticipants' => $rankingParticipants,
            'lastTournaments' => $lastTournaments,
            'upcomingTournaments' => $upcomingTournaments,
            'rankingLevels' => $rankingLevels
        ];
    }

    /**
     * @Route("/settings")
     * @Template()
     */
    public function settingsAction(Request $request)
    {
        $user = $this->getUser();
        $formPersonnal = $this->createForm(PersonnalInformationsSettingsType::class, $user);
        $formSocial = $this->createForm(SocialSettingsType::class, $user);
        $formPassword = $this->createForm(PasswordSettingsType::class);

        $formPersonnal->handleRequest($request);
        $formSocial->handleRequest($request);
        $formPassword->handleRequest($request);
        if (
            $formPersonnal->isSubmitted() && $formPersonnal->isValid() ||
            $formSocial->isSubmitted() && $formSocial->isValid() ||
            $formPassword->isSubmitted() && $formPassword->isValid()
        ) {
            $em = $this->getDoctrine()->getManager();

            $alert = 'alert.success.edit_settings';

            if ($formPassword->isSubmitted()) {
                $oldPassword = $formPassword->get('password')->getData();
                $newPassword = $formPassword->get('new_password')->getData();

                $passwordEncoder = $this->get('security.password_encoder');

                $alert = 'alert.success.edit_password';

                if ($user->getPassword() === $passwordEncoder->encodePassword($user, $oldPassword)) {
                    $user->setPassword($passwordEncoder->encodePassword($user, $newPassword));
                } else {
                    $translator = $this->get('translator');
                    $formPassword->get('password')->addError(new FormError($translator->trans('form.settings.wrong_password')));
                }
            }

            if (0 === count($formPassword->get('password')->getErrors())) {
                $em->flush();

                if (isset($alert)) {
                    $this->addFlash('success', $alert);
                }

                return $this->redirectToRoute('user_user_settings');
            }
        }

        return [
            'user' => $user, 'formPersonnal' => $formPersonnal->createView(),
            'formSocial' => $formSocial->createView(), 'formPassword' => $formPassword->createView(),
        ];
    }

    /**
     * @Route("/login")
     * @Method({"GET"})
     * @Template()
     */
    public function loginAction(Request $request)
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('app_app_index');
        }

        $referer = $request->headers->get('referer');
        if ($referer) {
            $request->getSession()->set('user_referer', $referer);
        }

        $authenticationUtils = $this->get('security.authentication_utils');
        $error = $authenticationUtils->getLastAuthenticationError();
        $form = $this->createForm(LoginType::class, [$authenticationUtils->getLastUsername()]);

        return ['error' => $error, 'form' => $form->createView()];
    }

    /**
     * @Method({"POST"})
     * @Route("/login_check")
     */
    public function loginCheckAction()
    {
        throw new \RuntimeException(
            'You must configure the check path to be handled by the firewall
            using form_login in your security firewall configuration'
        );
    }

    /**
     * @Route("/register")
     * @Template()
     */
    public function registerAction(Request $request)
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('app_app_index');
        }

        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->get('security.password_encoder')->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            $user->setLocale($request->getLocale());

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $translator = $this->get('translator');
            $subject = $translator->trans('mail.subject.register_confirmation');
            $link = $this->generateUrl('user_user_registerconfirmation', ['token' => $user->getToken()], UrlGeneratorInterface::ABSOLUTE_URL);
            $body = $translator->trans('mail.body.register', ['%link%' => $link]);

            $this->get('app.mail')->send(null, [['name' => $user->getUsername(), 'email' => $user->getEmail()]], $subject, $body);

            $this->addFlash('success', 'alert.success.register');

            return $this->redirectToRoute('app_app_index');
        }

        return ['user' => $user, 'form' => $form->createView()];
    }

    /**
     * @Route("/register/confirmation/{token}")
     * @ParamConverter("user", options={"mapping": {"token": "token"}})
     * @Template("@User/User/register_confirmation.html.twig")
     */
    public function registerConfirmationAction(Request $request, User $user)
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('app_app_index');
        }

        $form = $this->createForm(RegistrationConfirmationType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setActivated(true);
            $user->generateToken();

            $this->addFlash('success', 'alert.success.register_confirmation');
            $this->get('user.notification')->add($user, Notification::TYPE_WELCOME, NotificationTemplate::WELCOME);

            $this->getDoctrine()->getManager()->flush();

            $this->authenticateUser($user);

            return $this->redirectToRoute('app_app_index');
        }

        return ['user' => $user, 'form' => $form->createView()];
    }

    /**
     * @Route("/forgot-password/{token}", defaults={"token": null})
     * @Route("/forgot-password")
     * @ParamConverter("user", options={"mapping": {"token": "token"}})
     * @Template("@User/User/forgot_password.html.twig")
     */
    public function forgotPasswordAction(Request $request, User $user = null)
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('app_app_index');
        }

        $formEmailOrUsername = $this->createForm(EmailOrUsernameType::class);
        $formPassword = $this->createForm(PasswordForgotType::class);

        $translator = $this->get('translator');
        $em = $this->getDoctrine()->getManager();

        $formEmailOrUsername->handleRequest($request);
        $formPassword->handleRequest($request);

        if ($formEmailOrUsername->isSubmitted() && $formEmailOrUsername->isValid()) {
            $emailOrUsername = $formEmailOrUsername->get('email_or_username')->getData();
            $user = $em->getRepository(User::class)->findOneByEmailOrUsername($emailOrUsername);

            if ($user) {
                $link = $this->generateUrl('user_user_forgotpassword', ['token' => $user->getToken()], UrlGeneratorInterface::ABSOLUTE_URL);

                $mail = $this->get('app.mail');
                $mail->send(
                    null,
                    [['name' => $user->getUsername(), 'email' => $user->getEmail()]],
                    $translator->trans('mail.subject.forgot_password'),
                    $translator->trans('mail.body.forgot_password', ['%link%' => $link])
                );
            } else {
                $formEmailOrUsername->get('email_or_username')->addError(new FormError($translator->trans('form.forgot_password.no_user')));
            }

            if (0 === count($formEmailOrUsername->get('email_or_username')->getErrors())) {
                $em->flush();
                $this->addFlash('success', 'alert.success.forgot_password.email_sent');

                return $this->redirectToRoute('app_app_index');
            }
        }

        if ($formPassword->isSubmitted() && $formPassword->isValid() && null !== $user) {
            $user->setPassword($this->get('security.password_encoder')->encodePassword(
                $user, $formPassword->get('password')->getData()
            ));
            $user->generateToken();

            $em->flush();

            $this->addFlash('success', 'alert.success.forgot_password.password_changed');

            return $this->redirectToRoute('user_user_login');
        }

        return ['user' => $user, 'formEmailOrUsername' => $formEmailOrUsername->createView(), 'formPassword' => $formPassword->createView()];
    }

    /**
     * @Method({"GET"})
     * @Route("/logout")
     */
    public function logoutAction()
    {
        throw new \RuntimeException('You must activate the logout in your security firewall configuration');
    }

    /**
     * @param User $user
     */
    private function authenticateUser(User $user)
    {
        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());

        $this->get('security.token_storage')->setToken($token);
    }
}
