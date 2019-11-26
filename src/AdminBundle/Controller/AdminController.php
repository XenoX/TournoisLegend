<?php

namespace AdminBundle\Controller;

use AppBundle\Entity\Game;
use AppBundle\Entity\Mode;
use AppBundle\Entity\Tag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use TournamentBundle\Entity\Description;
use TournamentBundle\Entity\Lan;
use TournamentBundle\Entity\Mail;
use TournamentBundle\Entity\Organizer;
use TournamentBundle\Entity\Ranking;
use TournamentBundle\Entity\RankingLevel;
use TournamentBundle\Entity\Reward;
use TournamentBundle\Entity\Rules;
use TournamentBundle\Entity\Tournament;
use TournamentBundle\Workflow\TournamentWorkflow;
use UserBundle\Entity\NotificationTemplate;
use UserBundle\Entity\Team;
use UserBundle\Entity\User;

/**
 * Class AdminController
 * @package AdminBundle\Controller
 */
class AdminController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $now = new \DateTimeImmutable();

        $userCount = $em->getRepository(User::class)->count([]);
        $userCountDay = $em->getRepository(User::class)->findByDayOfYear((int) $now->format('z'));
        $userCountWeek = $em->getRepository(User::class)->findByWeekOfYear((int) $now->format('W'));
        $userCountMonth = $em->getRepository(User::class)->findByMonthOfYear((int) $now->format('m'));
        $teamCount = $em->getRepository(Team::class)->count([]);
        $tournamentCount = $em->getRepository(Tournament::class)->count([]);

        $lastUsers = $em->getRepository(User::class)->findBy([], ['id' => 'DESC'], 8);
        $lastTeams = $em->getRepository(Team::class)->findBy([], ['id' => 'DESC'], 8);

        $inProgressTournaments = $em->getRepository(Tournament::class)->findBy(['state' => TournamentWorkflow::STATES_IN_PROGRESS]);
        $weekTournaments = $em->getRepository(Tournament::class)->findByWeekNumber((int) $now->format('W'));
        $nextWeekTournaments = $em->getRepository(Tournament::class)->findByWeekNumber((int) $now->format('W') + 1);

        return [
            'userCount' => $userCount, 'teamCount' => $teamCount, 'tournamentCount' => $tournamentCount,
            'lastUsers' => $lastUsers, 'lastTeams' => $lastTeams, 'inProgressTournaments' => $inProgressTournaments,
            'weekTournaments' => $weekTournaments, 'nextWeekTournaments' => $nextWeekTournaments,
            'userCountDay' => $userCountDay, 'userCountWeek' => $userCountWeek, 'userCountMonth' => $userCountMonth
        ];
    }

    /**
     * @Route("/data/dashboard")
     * @Template()
     */
    public function dataAction()
    {
        $em = $this->getDoctrine()->getManager();

        $games = $em->getRepository(Game::class)->findAll();
        $modes = $em->getRepository(Mode::class)->findAll();
        $tags = $em->getRepository(Tag::class)->findAll();
        $templates = $em->getRepository(NotificationTemplate::class)->findAll();

        return ['games' => $games, 'modes' => $modes, 'tags' => $tags, 'templates' => $templates];
    }

    /**
     * @Route("/tournament/dashboard")
     * @Template()
     */
    public function tournamentAction()
    {
        $em = $this->getDoctrine()->getManager();

        $tournamentsNotStarted = $em->getRepository(Tournament::class)->findBy(['state' => TournamentWorkflow::STATES_BEFORE_CHECKIN]);
        $tournamentService = $this->get('tournament');
        foreach ($tournamentsNotStarted as $tournamentToCheck) {
            $tournamentService->changeStateIfNeeded($tournamentToCheck);
        }

        $em->flush();

        $initTournaments = $em->getRepository(Tournament::class)->findBy([
            'state' => TournamentWorkflow::STATE_INIT
        ]);
        $nextTournaments = $em->getRepository(Tournament::class)->findBy([
            'state' => TournamentWorkflow::STATE_REGISTRATION
        ]);
        $inProgressTournaments = $em->getRepository(Tournament::class)->findBy([
            'state' => TournamentWorkflow::STATES_IN_PROGRESS
        ]);
        $doneTournaments = $em->getRepository(Tournament::class)
            ->findBy(
                ['state' => TournamentWorkflow::STATE_DONE],
                ['id' => 'DESC']
            )
        ;
        $descriptions = $em->getRepository(Description::class)->findAll();
        $rewards = $em->getRepository(Reward::class)->findBy(['lan' => false]);
        /** @var Organizer[] $organizers */
        $organizers = $em->getRepository(Organizer::class)->findAll();
        $rules = $em->getRepository(Rules::class)->findAll();

        return [
            'initTournaments' => $initTournaments,
            'nextTournaments' => $nextTournaments,
            'inProgressTournaments' => $inProgressTournaments,
            'doneTournaments' => $doneTournaments,
            'descriptions' => $descriptions,
            'rewards' => $rewards,
            'organizers' => $organizers,
            'rules' => $rules
        ];
    }

    /**
     * @Route("/lan/dashboard")
     * @Template()
     */
    public function lanAction()
    {
        $em = $this->getDoctrine()->getManager();

        $lans = $em->getRepository(Lan::class)->findAll();
        $rewards = $em->getRepository(Reward::class)->findBy(['lan' => true]);
        $mails = $em->getRepository(Mail::class)->findAll();
        $rules = $em->getRepository(Rules::class)->findAll();

        return ['lans' => $lans, 'rewards' => $rewards, 'mails' => $mails, 'rules' => $rules];
    }

    /**
     * @Route("/ranking/dashboard")
     * @Template()
     */
    public function rankingAction()
    {
        $em = $this->getDoctrine()->getManager();

        $rankings = $em->getRepository(Ranking::class)->findAll();
        $rankingLevels = $em->getRepository(RankingLevel::class)->findAll();

        return ['rankings' => $rankings, 'rankingLevels' => $rankingLevels];
    }

    /**
     * @Route("/search")
     * @Method("POST")
     * @Template()
     */
    public function searchAction(Request $request)
    {
        $keyword = (string) $request->get('keyword');

        $em = $this->getDoctrine()->getManager();
        $tournaments = $em->getRepository(Tournament::class)->findByName($keyword, true);
        $users = $em->getRepository(User::class)->findByEmailOrUsername($keyword, true);
        $teams = $em->getRepository(Team::class)->findByName($keyword, true);

        if (1 === (count($tournaments) + count($users) + count($teams))) {
            if (1 === count($tournaments)) {
                return $this->redirectToRoute('admin_tournament_manage', ['tournament' => $tournaments[0]->getId()]);
            }
            if (1 === count($users)) {
                return $this->redirectToRoute('admin_user_update', ['user' => $users[0]->getId()]);
            }
            if (1 === count($teams)) {
                return $this->redirectToRoute('admin_team_update', ['team' => $teams[0]->getId()]);
            }
        }

        return [
            'tournaments' => $tournaments,
            'users' => $users,
            'teams' => $teams,
            'keyword' => $keyword,
            'keywordEncoded' => base64_encode($keyword)
        ];
    }
}
