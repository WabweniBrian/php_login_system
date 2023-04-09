<?php

$errors = [];

$data =
    [
        'username' => 'Joh',
        'email' => 'johndoe@example.com',
        'password' => "1234",
        'password_confirmation' => '1234',
    ];

$rules =
    [
        'username' => ['required', 'min:4', 'max:20'],
        'email' => ['required', 'email'],
        'password' => ['required', 'min:4'],
        'password_confirmation' => ['required', 'match:password'],
    ];

foreach ($rules as $field => $fieldRules) {
    foreach ($fieldRules as $rule) {

        echo '<h1>Rule Parts </h1>';
        $ruleParts = explode(':', $rule);
        echo '<pre>';
        print_r($ruleParts);
        echo '</pre>';


        echo '<h1>Rule Methods </h1>';
        $method = $ruleParts[0];
        echo ($method);

        echo '<h1>Params </h1>';
        $params = count($ruleParts) > 1 ? explode(',', $ruleParts[1]) : [];
        echo '<pre>';
        print_r($params);
        echo '</pre>';
        array_unshift($params, $data[$field]);

        echo '<h1>Params Updated </h1>';
        $params = count($ruleParts) > 1 ? explode(',', $ruleParts[1]) : [];
        echo '<pre>';
        print_r($params);
        echo '</pre>';
        // if (!call_user_func_array([self::class, $method], $params)) {
        $errors[$field] = getMessage($field, $method, $params);
        //     break;
        // } else {
        //     $errors[$field] = '';
        // }
    }

    echo '<br>';
    echo '<br>';
    echo '<br>';
}

function getMessage($field, $rule, $params)
{
    $messages = [
        'required' => 'The ' . $field . ' field is required.',
        'email' => 'The ' . $field . ' field must be a valid email address.',
        'min' => 'The ' . $field . ' field must be at least ' . (isset($params[1]) ? $params[1] : '') . ' characters.',
        'max' => 'The ' . $field . ' field must not exceed ' . (isset($params[1]) ? $params[1] : '') . ' characters.',
        'match' => 'The ' . $field . ' field does not match the ' . (isset($params[1]) ? $params[1] : '') . ' field.'
    ];

    return $messages[$rule];
}


echo '<pre>';
print_r($errors);
echo '</pre>';