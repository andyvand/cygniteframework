namespace {%Apps%}\Controllers;

use Cygnite\Common\Input;
use Cygnite\FormBuilder\Form;
use Cygnite\Validation\Validator;
use Cygnite\AssetManager\Asset;
use Cygnite\Common\UrlManager\Url;
use Cygnite\Foundation\Application;
use Cygnite\Mvc\Controller\AbstractBaseController;
use {%Apps%}\Components\Form\%ControllerName%Form;
use {%Apps%}\Models\%StaticModelName%;

/**
* This file is generated by Cygnite Crud Generator
* You may alter code to fit your needs
*/

class %ControllerName%Controller extends AbstractBaseController
{
    /**
    * --------------------------------------------------------------------------
    * The %controllerName% Controller
    *--------------------------------------------------------------------------
    *  This controller respond to uri beginning with %controllerName% and also
    *  respond to root url like "%controllerName%/index"
    *
    * Your GET request of "%controllerName%/form" will respond like below -
    *
    *      public function formAction()
    *     {
    *            echo "Cygnite : Hello ! World ";
    *     }
    * Note: By default cygnite doesn't allow you to pass query string in url, which
    * consider as bad url format.
    *
    * You can also pass parameters into the function as below-
    * Your request to  "%controllerName%/index/2134" will pass to
    *
    *      public function indexAction($id = ")
    *      {
    *             echo "Cygnite : Your user Id is $id";
    *      }
    * In case if you are not able to access parameters passed into method
    * directly as above, you can also get the uri segment
    *  echo Url::segment(3);
    *
    * That's it you are ready to start your awesome application with Cygnite Framework.
    *
    */

    // Plain layout
    protected $layout = 'layout.base';

    /**
    * Your constructor.
    * @access public
    */
    public function __construct()
    {
        parent::__construct();
    }

    /**
    * Default method for your controller. Render welcome page to user.
    * @access public
    *
    */
    public function indexAction()
    {
        $%controllerName% = array();
        $%controllerName% = %StaticModelName%::all(
            array(
                'orderBy' => '{%primaryKey%} desc',
                /*'paginate' => array(
                    'limit' => Url::segment(3)
                )*/
            )
        );

        $this->render('index', array(
            'records' => $%controllerName%,
            'links' => %StaticModelName%::createLinks(),
            'title' => 'Cygnite Framework - Crud Application'
        ));
    }

    /**
    * Handle add or update action
    * @param $id null
    */
    public function typeAction($id = null)
    {
        $input = Input::make();

        $errors =  $validator = null;

        //Check is form posted
        if ($input->hasPost('btnSubmit') == true) {

            $validator = null;
            //Set Form validation rules
            $validator = Validator::instance(
                $input,
                function ($validate) {

                    $validate%addRule%

                    return $validate;
                }
            );

            //Run validation
            if ($validator->run()) {

                // If id null we will get model object to insert
                // else we will find by primary key to update form database
                if ($id == null || $id == '') {
                    $%modelName% = new %StaticModelName%();
                } else {
                    $%modelName% = %StaticModelName%::find($id);
                }

                // get post array value except the submit button
                $postArray = $input->except('btnSubmit')->post();

                 %modelColumns%

                // Save form details
                if ($%modelName%->save()) {
                    $this->setFlash('success', '%controllerName% saved successfully!')
                         ->redirectTo('%controllerName%/index/'.Url::segment(4));
                } else {
                    $this->setFlash('error', 'Error occured while saving %controllerName%!')
                         ->redirectTo('%controllerName%/index/'.Url::segment(4));
                }

            } else {
                //validation error here
                $errors = $validator->getErrors();
            }
        }

        $form = null;

        if (isset($id) && $id !== null) {
            $%controllerName% = array();
            $%controllerName% = %StaticModelName%::find($id);
            $form = new %ControllerName%Form($%controllerName%, Url::segment(4));
            $form->errors = $errors;
            $form->validation = $validator;
            $this->edit($id, $form);
        } else {
            $form = new %ControllerName%Form();
            $form->errors = $errors;
            //Set the validator instance to handle validation errors
            $form->validation = $validator;
            $this->add($form);
        }
    }

    /**
    * Add a new Product view page
    * @param type $form
    */
    private function add($form)
    {
        // Since our all all logic is in controller
        // We can also use same view page for create and update
        $this->render('create', array(
            'form' => $form->buildForm()->render(),
            'validation_errors' => $form->errors,
            'title' => 'Add a new %controllerName%'
        ));
    }

    /**
    * Call update view page
    * @param type $id
    * @param type $form object
    */
    private function edit($id, $form)
    {
        // Since our all all logic is in controller
        // You can also use same view page for create and update
        $this->render('update', array(
            'form' => $form->buildForm()->render(),
            'validation_errors' => $form->errors,
            'title' => 'Update the %controllerName%'
        ));
    }

    /**
    *  Display product details
    * @param type $id
    */
    public function showAction($id)
    {
        $%modelName% = %StaticModelName%::find($id);

        $this->render('view', array(
            'record' => $%modelName%,
            'title' => 'Show the %controllerName%'
        ));
    }

    /**
    * Delete %controllerName% using id
    *
    * @param type $id
    */
    public function deleteAction($id)
    {
        $%controllerName% = new %StaticModelName%();

        if ($%controllerName%->trash($id) == true) {
            $this->setFlash('success', '%controllerName% Deleted Successfully!')
                 ->redirectTo('%controllerName%/');
        } else {
            $this->setFlash('error', 'Error Occured while deleting %controllerName%!')
                 ->redirectTo('%controllerName%/');
        }
    }

}//End of your %controllerName% controller
