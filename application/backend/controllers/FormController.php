<?php

namespace app\backend\controllers;

use app\models\Form;
use app\models\Object;
use app\models\ObjectPropertyGroup;
use app\models\PropertyGroup;
use app\models\Submission;
use Yii;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class FormController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['form manage'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new Form();
        $dataProvider = $searchModel->search($_GET);

        return $this->render(
            'index',
            [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
            ]
        );
    }

    public function actionEdit($id = null)
    {
        $model = new Form();
        if ($id > 0) {
            $model = Form::findOne($id);
        }

        /** @var \app\models\Object $object */
        $object = Object::getForClass(Form::className());

        $propIds = (new Query())->select('property_group_id')
            ->from([ObjectPropertyGroup::tableName()])
            ->where(
                [
                    'and',
                    'object_id = :object',
                    'object_model_id = :id'
                ],
                [
                    ':object' => $object->id,
                    ':id' => $id
                ]
            )->column();

        $post = \Yii::$app->request->post();
        $properties = isset($post['Form']['properties']) ? $post['Form']['properties'] : [];

        if ($model->load($post) && $model->validate()) {
            if ($model->save()) {
                $id = $model->id;
                $remove = [];
                $add = [];

                foreach ($propIds as $value) {
                    $key = array_search($value, $properties);
                    if ($key === false) {
                        $remove[] = $value;
                    } else {
                        unset($properties[$key]);
                    }
                }
                foreach ($properties as $value) {
                    $add[] = [
                        $value,
                        $object->id,
                        $id
                    ];
                }

                Yii::$app->db->createCommand()->delete(
                    ObjectPropertyGroup::tableName(),
                    [
                        'and',
                        'object_id = :object',
                        'object_model_id = :id',
                        ['in', 'property_group_id', $remove]
                    ],
                    [
                        ':object' => $object->id,
                        ':id' => $id,
                    ]
                )->execute();

                if (!empty($add)) {
                    Yii::$app->db->createCommand()->batchInsert(
                        ObjectPropertyGroup::tableName(),
                        ['property_group_id', 'object_id', 'object_model_id'],
                        $add
                    )->execute();
                }

                \Yii::$app->session->setFlash('info', Yii::t('app', 'Object saved'));
                return $this->redirect(['/backend/form/edit', 'id' => $model->id]);
            } else {
                \Yii::$app->session->setFlash('error', Yii::t('app', 'Cannot update data'));
            }
        }

        $items = ArrayHelper::map(
            PropertyGroup::find()
                ->where(
                    'object_id = :object',
                    [
                        ':object' => $object->id,
                    ]
                )->all(),
            'id',
            'name'
        );

        return $this->render(
            'edit',
            [
                'model' => $model,
                'items' => $items,
                'selected' => $propIds,
            ]
        );
    }

    public function actionView($id)
    {
        $submission = new Submission();
        $data = $submission->search($_GET, $id);

        return $this->render(
            'view',
            [
                'searchModel' => $submission,
                'dataProvider' => $data,
            ]
        );
    }

    public function actionViewSubmission($id)
    {
        $submission = Submission::findOne($id);
        if ($submission === null) {
            throw new NotFoundHttpException('Submission not found');
        }

        return $this->render(
            'view-submission',
            [
                'submission' => $submission,
            ]
        );
    }
}
