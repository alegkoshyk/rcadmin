<?php
/**
 * Created by PhpStorm.
 * User: bada
 * Date: 02.12.2016
 * Time: 15:16
 */
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Finder\Finder;


/////

$app->match('/pages/api/types', function(Request $request) use($app) {
    if ($request->isMethod('GET')){
        $dbout = $app['dbs']['adminUser']->fetchALL("SELECT * FROM type");
        $data['types'] = $dbout;
    }
    if ($request->isMethod('POST')){
        $data = json_decode($request->getContent(), true);
        $app['dbs']['adminUser']->insert('type', array(
            'type_name' => $data['name'],
            'type_template' => $data['template'],
            'type_add_text_fields' => $data['text_fields'],
            'type_add_img_fields' => $data['img_fields']
        ));
        $fieldsTXTArr = preg_split("/[\s,]+/", $data['text_fields']);
        $dbTXTHeaders= $app['dbs']['adminUser']->fetchALL("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'pages'AND DATA_TYPE = 'text'");
        foreach ( $fieldsTXTArr as $field ){
            if (in_array(array('COLUMN_NAME' => $field), $dbTXTHeaders) == false && !empty($field)){
                $sql= "ALTER TABLE pages ADD ".$field." TEXT";
                $app['dbs']['adminUser']->executeQuery($sql);
            }
        }
        $fieldsImgArr = preg_split("/[\s,]+/", $data['img_fields']);
        $dbImgHeaders= $app['dbs']['adminUser']->fetchALL("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'pages'AND DATA_TYPE = 'longtext'");
        foreach ( $fieldsImgArr as $field ){
            if (in_array(array('COLUMN_NAME' => $field), $dbImgHeaders) == false && !empty($field)){
                $sql= "ALTER TABLE pages ADD ".$field." LONGTEXT";
                $app['dbs']['adminUser']->executeQuery($sql);
            }
        }
    }

    return $app->json($data);
}, 'GET|POST');


/////////////////////////////////////////////////////////////////////
$app->match('/pages/api/types/{id}', function($id,Request $request) use($app) {
    $data = json_decode($request->getContent(), true);
    $request->request->replace(is_array($data) ? $data : array());
    if ($request->isMethod('GET')){
        $data = $app['dbs']['adminUser']->fetchALL("SELECT * FROM type WHERE type_id = $id");

    }
    //$sqltobase = "UPDATE 'type' SET 'type_template' = ?, 'type_add_text_fields' = ?, 'type_add_img_fields' = ?, 'type_name' =? WHERE 'id' = ?";
    // $app['dbs']['adminUser']->executeQuery($sqltobase,array( strval($data['template']),strval($data['additionalTextField']),strval($data['additionalImgField']),strval($data['type_name']),$id),array());

    if ($request->isMethod('PUT' | 'POST')){
        //$app['dbs']['adminUser']->executeQuery($sqltobase,array( strval($data['template']),strval($data['additionalTextField']),strval($data['additionalImgField']),strval($data['type_name']),$id));
        $dbin = array(
            'type_template' => strval($data['template']),
            'type_add_text_fields' => strval($data['additionalTextField']),
            'type_add_img_fields' => strval($data['additionalImgField']),
            'type_name' => strval($data['type_name']));
        $app['dbs']['adminUser']->update('type', $dbin, array('type_id' =>intval($id)));

        $fieldsTXTArr = preg_split("/[\s,]+/", $data['additionalTextField']);
        $dbTXTHeaders= $app['dbs']['adminUser']->fetchALL("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'pages'AND DATA_TYPE = 'text'");
        foreach ( $fieldsTXTArr as $field ){
            if (in_array(array('COLUMN_NAME' => $field), $dbTXTHeaders) == false && !empty($field)){
                $sql= "ALTER TABLE pages ADD ".$field." TEXT";
                $app['dbs']['adminUser']->executeQuery($sql);
            }
        }
        $fieldsImgArr = preg_split("/[\s,]+/", $data['additionalImgField']);
        $dbImgHeaders= $app['dbs']['adminUser']->fetchALL("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'pages'AND DATA_TYPE = 'longtext'");
        foreach ( $fieldsImgArr as $field ){
            if (in_array(array('COLUMN_NAME' => $field), $dbImgHeaders) == false && !empty($field)){
                $sql= "ALTER TABLE pages ADD ".$field." LONGTEXT";
                $app['dbs']['adminUser']->executeQuery($sql);
            }
        }
        return $app->json('hello');
    }
    if ($request->isMethod('DELETE')){
        $data = json_decode($request->getContent(), true);
        $app['dbs']['adminUser']->delete('type', array('id' => $id));
    }
    return $app->json($data);
}, 'GET|POST|PUT|DELETE')
    ->after(function (Request $request, Response $response) {
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'POST, GET, PUT, OPTIONS, PATCH, DELETE');
        $response->headers->set('Access-Control-Allow-Headers', 'X-CSRF-Token, X-Requested-With, Accept, Accept-Version, Content-Length, Content-MD5, Content-Type, Date, X-Api-Version, X-HTTP-Method-Override, Origin');
    });
//////////////////////////////////////////////////write file////////////////////////////////////////////////////////////
$app->match('/admin/firstrun', function(Request $request) use($app){
    $form = $app['form.factory']
        ->createBuilder('form')
        ->add('HostName','text',array('label' => 'Host name', 'required' => true))
        ->add('DbName','text',array('label' => 'Database name', 'required' => true))
        ->add('Username','text',array('label' => 'Username', 'required' => true))
        ->add('Password','text',array('label' => 'Password', 'required' => true))
        ->getForm();
    $request = $app['request'];
    if ($request->isMethod('POST')) {
        $form->bind($request);
        if ($form->isValid()) {
            $formInput=$request->get($form->getName());
            $insData="<?php
            ".'$inp'." = array(
    'dbs.options' => array (
                'adminUser' => array(
                    'driver'    => 'pdo_mysql',
                    'host'      => '".$formInput['HostName']."',
                    'dbname'    => '".$formInput['DbName']."',
                    'user'      => '".$formInput['Username']."',
                    'password'  => '".$formInput['Password']."',
                    'charset'   => 'utf8mb4',
                ),
                'anyUser' => array(
                    'driver'    => 'pdo_mysql',
                    'host'      => '".$formInput['HostName']."',
                    'dbname'    => '".$formInput['DbName']."',
                    'user'      => '%',
                    'password'  => null,
                    'charset'   => 'utf8mb4',
                ),
            ));";
            $filename = 'dbcnf.php';
            file_put_contents($filename, $insData);
        }
        return $app->redirect($app['url_generator']->generate('firstrun2'));
    }
    $response =  $app['twig']->render(
        'index.html.twig',
        array(
            'title' => 'Step 1. Creating database configs.',
            'message' => 'Input your database configuration data',
            'btn_name' => 'Send configs',
            'form' => $form->createView()
        )
    );
    return $response;
});
/////////////////////////////////////////////////////create initial db//////////////////////////////////////////////////
$app->match('admin/firstrun2', function (Request $request) use ($app){
    $form = $app['form.factory']
        ->createBuilder('form')
        ->getForm();
    $request = $app['request'];
    if (file_exists('dbcnf.php') == true){
        $message = 'All is ok, you can create initial database';
    } else {
        $message = 'somethig went wrong, database configs were not created';
    }
    if ($request->isMethod('POST')) {
        $form->bind($request);
        if ($form->isValid()) {
            ///////// type table
            $typeSchema = new \Doctrine\DBAL\Schema\Schema();
            $typeTable = $typeSchema->createTable("type");
            $typeTable->addColumn("type_id", "integer", array("autoincrement" => true));
            $typeTable->addColumn("type_name", "string");
            $typeTable->addColumn("type_template", "string");
            $typeTable->addColumn("type_add_text_fields", "string");
            $typeTable->addColumn("type_add_img_fields", "string");
            $typeTable->addColumn("datetime", "datetime", array("default" => "CURRENT_TIMESTAMP"));
            $typeTable->setPrimaryKey(array("type_id"));
            $typeTable->addUniqueIndex(array("type_id"));
            $typeTable->addUniqueIndex(array("type_name"));
            $app['dbs']['adminUser']->getSchemaManager()->createTable($typeTable);
            ////////// users table
            $usersSchema = new \Doctrine\DBAL\Schema\Schema();
            $usersTable = $usersSchema->createTable("users");
            $usersTable->addColumn("id_user", "integer", array("autoincrement" => true));
            $usersTable->addColumn("username", "string");
            $usersTable->addColumn("password", "string");
            $usersTable->addColumn("email", "string");
            $usersTable->addColumn("phone", "string");
            $usersTable->addColumn("status", "string");
            $usersTable->addColumn("subject", "string");
            $usersTable->addColumn("datetime", "datetime", array("default" => "CURRENT_TIMESTAMP"));
            $usersTable->setPrimaryKey(array("id_user"));
            $usersTable->addUniqueIndex(array("id_user"));
            $usersTable->addUniqueIndex(array("username"));
            $app['dbs']['adminUser']->getSchemaManager()->createTable($usersTable);
            //////// users_adm table
            $users_admSchema = new \Doctrine\DBAL\Schema\Schema();
            $users_admTable = $users_admSchema->createTable("users_adm");
            $users_admTable->addColumn("id_user_admin", "integer", array("autoincrement" => true));
            $users_admTable->addColumn("username", "string");
            $users_admTable->addColumn("password", "string");
            $users_admTable->addColumn("permissions", "string");
            $users_admTable->addColumn("datetime", "datetime", array("default" => "CURRENT_TIMESTAMP"));
            $users_admTable->setPrimaryKey(array("id_user_admin"));
            $users_admTable->addUniqueIndex(array("id_user_admin"));
            $users_admTable->addUniqueIndex(array("username"));
            $app['dbs']['adminUser']->getSchemaManager()->createTable($users_admTable);
            ///////// templates table
            $templatesSchema = new \Doctrine\DBAL\Schema\Schema();
            $templatesTable = $templatesSchema->createTable("templates");
            $templatesTable->addColumn("template_id", "integer", array("autoincrement" => true));
            $templatesTable->addColumn("template_name", "string");
            $templatesTable->addColumn("template_file", "string");
            $templatesTable->addColumn("template_description", "string");
            $templatesTable->addColumn("datetime", "datetime", array("default" => "CURRENT_TIMESTAMP"));
            $templatesTable->setPrimaryKey(array("template_id"));
            $templatesTable->addUniqueIndex(array("template_id"));
            $templatesTable->addUniqueIndex(array("template_name"));
            $app['dbs']['adminUser']->getSchemaManager()->createTable($templatesTable);
            /////////pages table
            $pagesSchema = new \Doctrine\DBAL\Schema\Schema();
            $pagesTable = $pagesSchema->createTable("pages");
            $pagesTable->addColumn("page_id", "integer", array("autoincrement" => true));
            $pagesTable->addColumn("page_url", "string");
            $pagesTable->addColumn("page_name", "string");
            $pagesTable->addColumn("page_title", "string");
            $pagesTable->addColumn("description", "string");
            $pagesTable->addColumn("page_template", "string");
            $pagesTable->addColumn("content", "string");
            $pagesTable->addColumn("short_content", "string");
            $pagesTable->addColumn("img", "array");
            $pagesTable->addColumn("ceo_img", "string");
            $pagesTable->addColumn("subpages", "string");
            $pagesTable->addColumn("page_type", "string");
            $pagesTable->addColumn("rank", "string");
            $pagesTable->addColumn("active", "boolean");
            $pagesTable->addColumn("datetime", "datetime", array("default" => "CURRENT_TIMESTAMP"));
            $pagesTable->setPrimaryKey(array("page_id"));
            $pagesTable->addUniqueIndex(array("page_id"));
            $pagesTable->addUniqueIndex(array("page_url"));
            $app['dbs']['adminUser']->getSchemaManager()->createTable($pagesTable);

            return $app->redirect($app['url_generator']->generate('admin'));
        }
    }
    $response =  $app['twig']->render(
        'index.html.twig',
        array(
            'title' => 'Step2. Installing new database.',
            'message' => $message,
            'btn_name' => 'Create database',
            'form' => $form->createView()
        )
    );
    return $response;
})->bind("firstrun2");

///////////////////////////////////////////// page generator ///////////////////////////////////////////////////////////
$app->get('/{url}', function ($url) use ($app) {
    $sql = "SELECT * FROM pages WHERE page_url = ?";
    $dbout = $app['dbs']['anyUser']->fetchALL($sql,array((string) $url));
    if ($dbout == !null) {
        return $dbout;
    } else {
        return $app->abort(404);
    }
});

