<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends Controller
{
    /**
     * @Route("/admin/dashboard", name="admin_dashboard")
     */
    
    public function index(UserRepository $userRepo)  // à l'url home, cette fct° sera exécutée :
    {   // ns on va faire en sorte que notre appli return du HTML
        
        $userList = $userRepo->findAll(); 
        // $userRepo est passé automtqt en paramètre par Sfy
        // -> injection de dépendances. On a dc pas à l'instancier ns-même.
        // $userRepo effectuera ici un SELECT * FROM user ...
        
        // Dc a récup list user ac findAll et on transfère à notre vue ac render :
        return $this->render("admin/dashboard.html.twig", [     // penser à changer home > admin/dasboard...
            'users' => $userList
        ]); 
        // Dessine-moi ce template (dashboard.html.twig) ac la variable msg qui vaudra message
    }    
    
    /**
     * @Route("/admin/user/delete/{id}", name="delete_user")
     */
    // id = param dynamiq, on récupère ce qui correspond à cet id de user dessous : id converti en user 
    public function deleteUser(User $user, ObjectManager $manager)  
    // qd on commence à taper Obj... > longue lg > ctrl shift i
    {   // id d'annotation transformé en argument $user ds fct
        $manager->remove($user);
        $manager->flush();
        return $this->redirectToRoute('admin_dashboard');
    }
    
    /**
     * @Route("/admin/user/add", name="add_user")
     * @Route("/admin/user/edit/{id}", name="edit_user")
     */
    public function editUser(Request $request, ObjectManager $manager, User $user = null) // se produira ds 2 cas : à l'ajout ou si erreur
    {   // request recueille // Objest Manager permet d'entrer ou suppr en BDD
        if($user === null){             //c'est le user de Entity ici
            $user = new User();
        }
        $formUser = $this->createForm(UserType::class, $user)  //on précise le type puis les données
            ->add('Envoyer', SubmitType::class);
        // ...todo : validation
        
        $formUser->handleRequest($request); // déclenche la gestion du formulaire
        
        if($formUser->isSubmitted() && $formUser->isValid()) {  // si le btn envoyé cliqué
            // enregistrement de notre user
            $user->setRegisterDate(new \DateTime('now')); // date d'enregistrt
            $user->setRoles('ROLE_USER');                // rôle (admin ou pas)
            $manager->persist($user);                    // insert ds BDD
            $manager->flush();                           // remettre à 0
            
            return $this->redirectToRoute('admin_dashboard');
            // on redirige vers l'admin dashboard
        }
        
        return $this->render('admin/edit_user.html.twig',[
           'form' => $formUser->createView()        
        ]);
    }
    
    
}
