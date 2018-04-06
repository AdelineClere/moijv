<?php

namespace App\Controller;

use App\Entity\Loan;
use App\Entity\Product;
use App\Form\LoanType;
use DateTime;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

    // on crée Route gén car on va tout préfixer par loan
/**
 * @Route("/loan")
 */
class LoanController extends Controller
{
        // en param de la fct on récup pdt ( car /{id} )
        // id = du pdt / la, j'ajoute à partir de ça
    /**
     * @Route("/{id}/add", name="add_loan")
     */
    public function add(Product $product, ObjectManager $manager)   //fct qui s'exécutera qd je taperai  /loan/add
    {
        // je créé un nv loan et l'enregistre en DB
        $loan = new Loan();
        $loan->setDateAdd(new DateTime('now'))   // si pas ctrl shift i > \DateTime
                ->setStatus('pending')
                ->setProduct($product)
                ->setLoaner($this->getUser()); 
        $manager->persist($loan);           // pas besoin déclaré id, car Doctrine le donne autom, qd on persist
        $manager->flush();

        return $this->redirectToRoute('edit_loan', ['id' => $loan->getId()]);
        // je crée un nw loan , associe a user etc..
        // puis redirige vers route qui me permettra d'aff ce Loan, grace à id dessous..?
    }
    
        // id = de l'emprunt / La j'ajoute ça ({id} après)
        // edit_loan et on lui passe le param id du Loan
    /**
     * @Route("/edit/{id}", name="edit_loan")
     */
    public function edit(Loan $loan, Request $request, ObjectManager $manager)  
    // méthode pour éditer un loan / request car on aura formulaire / ObjM pour sauvg. notre loan
    {
        $loanForm = $this->createForm(LoanType::class, $loan) // (\App\Form\LoanType::class, $loan)>type de form + donnée ds le formul
                ->add('Valider', SubmitType::class); // créer btn submit
        $loanForm->handleRequest($request); // je dem à mon form de gérer req (POST à priori)de mon form
            
        if($loanForm->isSubmitted() && $loanForm->isValid()) {
           $manager->persist($loan);    // on prend notre $manager, on persist et redirige vers Route
           $manager->flush();
           return $this->redirectToRoute('home');
        }
        
        return $this->render('loan/edit.html.twig',[
            'form' => $loanForm->createView()    
                    // je transmet à template (mais sera pas du tout mon LoanForm) > grâce à un tablo associatif
        ]);        
    }
    
    
    
    
    
    
}
