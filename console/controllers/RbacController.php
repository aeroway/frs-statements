<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;

class RbacController extends Controller
{
    public function actionInit()
    {
        $auth = Yii::$app->authManager;

        // add permission "editMfc"
        $editMfc = $auth->createPermission('editMfc');
        $editMfc->description = 'Can do actions as MFC user';
        $auth->add($editMfc);

        // add permission "editZkp"
        $editZkp = $auth->createPermission('editZkp');
        $editZkp->description = 'Can do actions as ZKP user';
        $auth->add($editZkp);

        // add permission "editRosreestr"
        $editRosreestr = $auth->createPermission('editRosreestr');
        $editRosreestr->description = 'Can do actions as Rosreestr user';
        $auth->add($editRosreestr);

        // add permission "addAudit"
        $addAudit = $auth->createPermission('addAudit');
        $addAudit->description = 'Can view more info';
        $auth->add($addAudit);

        // add permission "confirmExtDocs"
        $confirmExtDocs = $auth->createPermission('confirmExtDocs');
        $confirmExtDocs->description = 'Can confirm extraterritorial documents';
        $auth->add($confirmExtDocs);

        // add role "mfc" and give it permission "editMfc"
        $mfc = $auth->createRole('mfc');
        $auth->add($mfc);
        $auth->addChild($mfc, $editMfc);

        // add role "zkp" and give it permission "editZkp"
        $zkp = $auth->createRole('zkp');
        $auth->add($zkp);
        $auth->addChild($zkp, $editZkp);

        // add role "rosreestr" and give it permission "editRosreestr"
        $rosreestr = $auth->createRole('rosreestr');
        $auth->add($rosreestr);
        $auth->addChild($rosreestr, $editRosreestr);

        // add role "Audit" and give it permission "addAudit"
        $audit = $auth->createRole('audit');
        $auth->add($audit);
        $auth->addChild($audit, $addAudit);

        // add role "extdocs" and give it permission "confirmExtDocs"
        $extdocs = $auth->createRole('extdocs');
        $auth->add($extdocs);
        $auth->addChild($extdocs, $confirmExtDocs);

        // set the role for users
        $auth->assign($zkp, 13);
        $auth->assign($mfc, 14);
        $auth->assign($audit, 15);
        $auth->assign($rosreestr, 17);
        $auth->assign($extdocs, 18);
    }
}