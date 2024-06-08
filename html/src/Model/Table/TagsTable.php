<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Tags Model
 *
 * @property \App\Model\Table\ArticlesTable&\Cake\ORM\Association\BelongsToMany $Articles
 *
 * @method \App\Model\Entity\Tag newEmptyEntity()
 * @method \App\Model\Entity\Tag newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Tag[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Tag get($primaryKey, $options = [])
 * @method \App\Model\Entity\Tag findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Tag patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Tag[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Tag|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Tag saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Tag[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Tag[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Tag[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Tag[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class TagsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('tags');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsToMany('Articles', [
            'foreignKey' => 'tag_id',
            'targetForeignKey' => 'article_id',
            'joinTable' => 'articles_tags',
        ]);
    }

    /**
     * mPOS連携レジ端末数上限変更時の入力チェック
     */
    public $validate_tag = [
        'request_check' => [
            'range' => [
                'rule' => ['range', -1, 3],
                'message' => '[スマレジ] mPOS連携レジ端末数上限は0～9999の値を入力してください。',
            ],
            // 'rangetest' => [
            //     'rule' => 'validateRangeOfTerminalCount',
            //     'message' => '[スマレジ] mPOS連携レジ端末数上限はレジ端末数上限以下を入力してください',
            // ],
        ],
        // この名前を変えるとなぜかわからんけどエラメッセージが変わる。他の関数を見てそう
        // しかも出てくるエラーが、[pos]請求開始日を指定してください・請求開始日は正しい日付ではありません、でどこにも定義されてない文字列みたい。
        // 'billing_start_date' => [
        //     'date' => [
        //         'rule' => ['date', 'ymd'],
        //         'message' => '[スマレジ] 請求開始日を正しく入力してください。！！！！！！！！！！！！',
        //     ],
        // ],
    ];



    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('title')
            ->maxLength('title', 3)
            ->allowEmptyString('title')
            ->add('title', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);



        return false;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['title'], ['allowMultipleNulls' => true]), ['errorField' => 'title']);

        return $rules;
    }
}
