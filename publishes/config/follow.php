<?php

/*
 * This file is part of the overtrue/laravel-follow
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

return [
    /*
     * Model class name of users.
     */
    'user_model' => \Telefonica\Models\Actors\Person::class,

    /*
     * Table name of users table.
     */
    'users_table_name' => 'persons',

    /*
     * Primary key of users table.
     */
    'users_table_primary_key' => 'code',

    /*
     * Foreign key of users table.
     */
    'users_table_foreign_key' => 'person_code', //'user_id',

    /*
     * Table name of followable relations.
     */
    'followable_table' => 'followables',

    /*
     * Prefix of many-to-many relation fields.
     */
    'morph_prefix' => 'followable',

    /*
     * Date format for created_at.
     */
    'date_format' => 'Y-m-d H:i:s',

    /*
     * Namespace of models.
     */
    'model_namespace' => 'App\\Models',
];
