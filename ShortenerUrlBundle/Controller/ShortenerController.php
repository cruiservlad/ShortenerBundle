<?php

namespace Cruiser\ShortenerUrlBundle\Controller;

use Cruiser\ShortenerUrlBundle\Entity\Shortener;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Shortener controller.
 *
 * @Route("/")
 */
class ShortenerController extends Controller
{
    private function open_url($url)
    {
        $url_c=parse_url($url);
     
        if (!empty($url_c['host']) and checkdnsrr($url_c['host']))
        {
            // Ответ сервера
            if ($otvet=@get_headers($url)){
                return substr($otvet[0], 9, 3);
            }
        }
        return false;     
    }

    private function getShortUrl($address)
    {
        // Check if URL existed
        $repository = $this->getDoctrine()->getRepository('CruiserShortenerUrlBundle:Shortener');
        $existed = $repository->findOneByUrl($address->getUrl());

        if($existed) {
          $shorturl = $existed->getShorturl();
        } else {

          // Create new Url Entity
          $url = new Shortener();
          $url->setUrl($address->getUrl());

          // Save URL to Database
          $em = $this->getDoctrine()->getManager();
          $em->persist($url);
          $em->flush();

          $existedShort = $repository->findOneByShorturl($address->getShorturl());
          if($existedShort) $shorturl = md5($url->getId());
          else $shorturl = $address->getShorturl();

          // Save Slug to database
          $url->setShorturl($shorturl);
          $em->persist($url);
          $em->flush();
        }
        return $shorturl;
    }

    /**
     * Lists all shortener entities.
     *
     * @Route("/", name="_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $shorteners = $em->getRepository('CruiserShortenerUrlBundle:Shortener')->findAll();
        $form = $this->createForm('Cruiser\ShortenerUrlBundle\Form\ShortenerType', $shortener);

        return $this->redirectToRoute('shortener/new.html.twig', array(
            'shortener' => $shortener,
            'form' => $form->createView(),
        ));
    }

    /**
     * Creates a new shortener entity.
     *
     * @Route("/new", name="_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $shortener = new Shortener();
        $form = $this->createForm('Cruiser\ShortenerUrlBundle\Form\ShortenerType', $shortener);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $this->open_url($shortener->getUrl())) {
            return $this->redirectToRoute('_show', array('shorturl' => $this->getShortUrl($shortener)));
        }

        return $this->render('shortener/new.html.twig', array(
            'shortener' => $shortener,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a shortener entity.
     *
     * @Route("/{shorturl}", name="_show")
     * @Method("GET")
     */
    public function showAction(Shortener $shortener)
    {
        //$deleteForm = $this->createDeleteForm($shortener);

        //processAction($shortener->getShorturl());
        /*$repository = $this->getDoctrine()->getRepository('CruiserShortenerUrlBundle:Shortener');
        $url = $repository->findOneByShorturl($shortener->getShorturl());
        $url->setHits(($url->getHits() + 1));
        $em = $this->getDoctrine()->getManager();
        $em->persist($url);
        $em->flush();*/
        return $this->render('shortener/show.html.twig', array(
            'shortener' => $shortener,
            //'delete_form' => $deleteForm->createView(),
        ));
        //return $this->redirect($url->getUrl(), 301);
    }

    /**
     * Finds and displays a shortener entity.
     *
     * @Route("/l/{shorturl}", name="_info")
     * @Method("GET")
     */
    public function infoAction(Shortener $shortener)
    {
        $repository = $this->getDoctrine()->getRepository('CruiserShortenerUrlBundle:Shortener');
        $url = $repository->findOneByShorturl($shortener->getShorturl());
        $url->setHits(($url->getHits() + 1));
        $em = $this->getDoctrine()->getManager();
        $em->persist($url);
        $em->flush();
        return $this->redirect($url->getUrl(), 301);
    }

    /**
     * Displays a form to edit an existing shortener entity.
     *
     * @Route("/{shorturl}/edit", name="_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Shortener $shortener)
    {
        $deleteForm = $this->createDeleteForm($shortener);
        $editForm = $this->createForm('Cruiser\ShortenerUrlBundle\Form\ShortenerType', $shortener);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('_edit', array('shorturl' => $shortener->getShorturl()));
        }

        return $this->render('shortener/edit.html.twig', array(
            'shortener' => $shortener,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a shortener entity.
     *
     * @Route("/{shorturl}", name="_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Shortener $shortener)
    {
        $form = $this->createDeleteForm($shortener);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($shortener);
            $em->flush();
        }

        return $this->redirectToRoute('_index');
    }

    /**
     * Creates a form to delete a shortener entity.
     *
     * @param Shortener $shortener The shortener entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Shortener $shortener)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('_delete', array('shorturl' => $shortener->getShorturl())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
