<?php

namespace App\Controller;

use App\Entity\Tag;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;


    // annot° pr tte la pg
/**
 * @Route("/tag")
 */
class TagController extends Controller
{
        // Cette route déclenchée par choix du tag par user > va appeler template du tag en question de tag/index.html.twig
        //  (.. va tenter un get by slug et va trouver un tag)
    /**
     * @Route("/{slug}/product", name="tag")       
     */
    public function product(Tag $tag) // on va appeler les pdts liés à ce tag qu'on a trouvé grâce au slug ds url
    {
        return $this->render('tag/product.html.twig', [
            'controller_name' => 'TagController',
        ]);
    }
}
