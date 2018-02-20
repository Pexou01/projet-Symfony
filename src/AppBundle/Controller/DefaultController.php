<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Entity\Contact;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @Route("/test")
     */
    public function testAction()
    {
        return new Response("test");
    }

    /**
     * @Route( "/hello/{age}/{name}/{firstName}",name="hellopage",
     *     defaults={"firstName"="toto", "name"="le héro"},
     *     requirements={"age"="\d{1,3}"}
     * )
     */
    public function hello($name, $firstName, $age)
    {
        return $this->render("default/hello.html.twig", [
            "name" => $name, "firstName" => $firstName, "age" => $age,
            "message" => "Symfony c'est super",
            "now" => new \DateTime()
        ]);
    }

    /**
     * @Route ("/fruit" , name="fruitpage")
     */
    public function fruit()
    {
        $fruit = ["fraise", "orange"];

        $food =[
            ["name" => "Pomme", "type" => "fruit", "edible" =>true],
        ["name" => "Radis", "type" => "légume", "edible" =>true],
        ["name" => "co2", "type" => "gaz", "edible" =>false],
        ["name" => "Canard", "type" => "viande", "edible" =>true],
        ["name" => "Marteau", "type" => "outil", "edible" =>false]
           ] ;
        return $this->render("default/fruit.html.twig", ["fruitList" => $fruit, "foodList" => $food]);
    }

    /**
     * @Route ("/new-contact", name="new_contactpage")
     */
    public function newConactAction(){
        $contact = new Contact();
        $contact->setName("fred")
            ->setFirstName("victor")
            ->setEmail("fred.victor@pontoise.org")
            ->setDateOfBirth(new \DateTime("1986-08-06"));

        //recuperation de l'entiter
        $em = $this->getDoctrine()->getManager();
        $em-> persist($contact);
        $em->flush();

        return $this->render("default/new-contact.html.twig", ["contact"=>$contact]);
    }

    /**
     * @Route("/add-article")
     */
    public function addArticlesAction()
    {
        $em = $this->getDoctrine()->getManager();

        $em->persist(new Article("Symfony 4 arrive", "dev", true));
        $em->persist(new Article("La nouvelle gaffe de Trump", "politique", false));
        $em->persist(new Article("Les sorties de la semaine", "cinéma", true));
        $em->persist(new Article("Doctrine et Symfony", "dev", true));
        $em->persist(new Article("Cours de Macron économie", "politique", false));
        $em->persist(new Article("AngulasJs vs ReactJS round 1", "dev", true));

        //equivalent du comit flush
        $em->flush();

        return new Response("articles chargés");
    }
        /**
         * @Route("/article-list/page-{page}/{category}",
         *     defaults={"category"="all", "page"=1}, requirements={"page"="\d+"},
         *     name="article_list")
         */
        public function showArticlesAction($category, $page){
            $repository = $this->getDoctrine()->getRepository("AppBundle:Article");

            $nbArticlePerPage = 2;

            if($category == 'all'){
                $articleList = $repository->findBy([],['category' => 'ASC']);
            }else {
                $articleList = $repository->findByCategory($category, ['category' => 'ASC'] );
            }

            $nbArticles = count($articleList);
            $nbPages = ceil($nbArticles / $nbArticlePerPage);
            $offset = $nbArticlePerPage * ($page-1);
            $articleList = array_slice($articleList, $offset, $nbArticlePerPage);

            return $this->render("default/article-list.html.twig", ["articleList" => $articleList,
                "nbPages" => $nbPages,
            "currentPage" => $page,
                "category" => $category
            ]);
        }
/**
 * @Route("/article-delete/{id}", requirements={"id"="\d+"})
 */
public function deleteArticleAction($id){
    //Récupération de l'article à surpimé
    $repository = $this->getDoctrine()->getRepository("AppBundle:Article");
    $article = $repository->findOneById($id);

    //supresion de l'entité
    $em = $this->getDoctrine()->getManager();
    $em->remove($article);
    $em->flush();
    return $this->redirect("/article-list");

}
/**
 * @Route("/article-details/{id}", name="article_details",requirements={"id"="\d+"})
 */
public function articleDetailsAction(Article $article){
    return $this->render("default/article-details.html.twig", ["article" =>$article]);
}
}
