$form = $app['form.factory']
->createBuilder('form')
->add('Username', 'text', array('max_length' => '100', 'required' => true))
->add('Password', 'password', array('required' => true))
->add('ConfirmPassword', 'password', array('required' => true))
->add('Permissions', 'choice', array(
'choices' => array(1=>'full', 2=>'create pages'),
'expanded' => false
))
->getForm();
$message = 'Hello admin ' . $app['session']->get('user')["username"] . '. You can create new admin user.';
if ($request->isMethod('POST')) {
$form->bind($request);
if ($form->isValid()) {
$formInput = $request->get($form->getName());
$sql = "SELECT username FROM users_adm WHERE username = ?";
if ($app['dbs']['adminUser']->fetchALL($sql, array((string) $formInput['Username']))==false){
if ($formInput['Password'] != $formInput['ConfirmPassword']) {
$message = 'password does not match';
} else {
$newUserName = $formInput['Username'];
$newUserPassword = $formInput['Password'];
$newUserPermissions = $formInput['Permissions'];
$app['dbs']['adminUser']->insert('admin_user', array(
'username' => $newUserName,
'password' => $newUserPassword,
'Permissions' => $newUserPermissions
));
$message = 'New admin user was successfully added!';
}
} else {$message = 'Admin user with such name already exists!';}
}
}
$response = $app['twig']->render(
'index.html.twig',
array(
'title' => 'Add new admin',
'message' => $message,
'btn_name' => 'Register',
'form' => $form->createView()
)
);
return $response;