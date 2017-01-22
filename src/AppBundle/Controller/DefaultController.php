<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Template()
     */
    public function indexAction()
    {
        return [];
    }

    /**
     * @Route("/shows/{id}", name="shows")
     * @Template()
     */
    public function showsAction($id)
    {
		$offset = $id * 8 - 8;
        $em = $this->get('doctrine')->getManager();
        $repo = $em->getRepository('AppBundle:TVShow');
        $shows = $repo->findBy(
            array(),
            array('name' => 'asc'),
            8,
            $offset
        );
		$em = $this->getDoctrine()->getManager();
        $query = $em->createQueryBuilder();
        $query->select('count(s.id) as somme')
            ->from('AppBundle:TVShow', 's');
        $res = $query->getQuery()->getSingleScalarResult();

        //nombre de pages
        $endPage = ceil($res / 8);

		return $this->render('AppBundle:Default:shows.html.twig', array(
            'shows' => $shows,
            'page' => $id,
            'endPage' => $endPage,
        ));
    }

    /**
     * @Route("/show/{id}", name="show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->get('doctrine')->getManager();
        $repo = $em->getRepository('AppBundle:TVShow');

        return [
            'show' => $repo->find($id)
        ];
    }

	/**
     * @Route("/search", name="search")
     * @Template()
     */
    public function searchAction(Request $request)
    {
        if ($request->getMethod() == "POST") {
            $search = $request->request->get('search');
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQueryBuilder();
            $query
                ->select('s')
                ->from('AppBundle:TVShow', 's')
                ->where('s.name LIKE :data OR s.synopsis LIKE :data')
                ->setParameter('data', '%'.$search.'%');
            $shows = $query->getQuery()->getResult();
        }
        return $this->render('AppBundle:Default:shows.html.twig', array(
            'shows' => $shows
        ));
    }

    /**
     * @Route("/calendar", name="calendar")
     * @Template()
     */
    public function calendarAction()
    {
        $date = new \DateTime();
        $em = $this->getDoctrine()->getManager();
        $queryEpisodes = $em->createQueryBuilder();
        $queryEpisodes->select('e')
            ->from('AppBundle:Episode', 'e')
            ->where('e.date >= :date')
            ->orderBy('e.date', 'ASC')
            ->setParameter('date', $date);
        $episodes = $queryEpisodes->getQuery()->getResult();

        return $this->render('AppBundle:Default:calendar.html.twig', array(
            'episodes' => $episodes
        ));
    }

    /**
     * @Route("/login", name="login")
     * @Template()
     */
    public function loginAction()
    {
        return [];
    }
}
