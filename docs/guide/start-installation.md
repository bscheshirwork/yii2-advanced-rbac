Установка шаблона yii2-advanced, модулей пользователей и ролей
==============

Ниже будет описан алгоритм создания примера, в папке `docs/guide` которого и находится эта инструкция

> Примечание: в примере приложение названо `yii2-advanced-rbac`

Необходимые зависимости:

Зависимость | Отвечает за
--- | --- | ---
[yiisoft/yii2-app-advanced](https://github.com/yiisoft/yii2-app-advanced) | шаблон advanced
[dektrium/yii2-user](https://github.com/dektrium/yii2-user/) | управление пользователями
[githubjeka/gui-rbac-yii2](https://github.com/githubjeka/gui-rbac-yii2) | графическое представление RBAC
[mdmsoft/yii2-admin](https://github.com/mdmsoft/yii2-admin) | управление RBAC


Установка шаблона advanced [yiisoft/yii2-app-advanced](https://github.com/yiisoft/yii2-app-advanced)
-----------------------

Для установки [шаблона advanced](https://github.com/yiisoft/yii2-app-advanced/blob/master/docs/guide/README.md)
используйте команды

```
composer global require "fxp/composer-asset-plugin:~1.1.1"
composer create-project --prefer-dist yiisoft/yii2-app-advanced yii2-advanced-rbac
```

> Примечание: подразумевается, что composer установлен глобально, например, следуя [инструкции](https://getcomposer.org/doc/00-intro.md#globally)

Дополнительная информация (например, nginx конфиг) доступна в [документации](https://github.com/yiisoft/yii2-app-advanced/blob/master/docs/guide/README.md)


> Примечание: Для отправки почты (сообщение о регистрации, восстановление пароля, подтверждение ночты в модуле пользователей)
необходимо настроить отправку почты

#### Настройки отправки почты:
В файле `yii2-advanced-rbac/environments/dev/common/config/main-local.php` укажите настройки `transport` либо `useFileTransport`
```
<?php
return [
    'components' => [
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'mail.youmailserver.com',
                'username' => 'username@youmailserver.com',
                'password' => 'password',
                'port' => '587',
                'encryption' => 'tls',
            ],
        ],
    ],
];
```

Важно! Для почтового сервера необходимо привести в соответствие логину (почтовому сервису) адреса отправителей
Необходимо изменть настройку в файле конфигурации
`server/environments/dev/common/config/params-local.php`
```
<?php
return [
    'adminEmail' => 'username@youmailserver.com',
];
```

Для настроек `prod`, скорее всего, эти адреса будут различные для разных частей, для этого необходимо
переопределить (либо удалить не отличающиеся от общих) настройки во всех локальных конфигах параметров.

#### Настройки базы данных:

Для работы с примером необходимо создать базу данных. Настройки соединения с этой базой записываются в тот же

`yii2-advanced-rbac/environments/dev/common/config/main-local.php`

```
<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=yii2advancedrbac',
            'username' => 'yii2advancedrbac',
            'password' => 'enter-password-here',
            'charset' => 'utf8',
        ],
    ],
];
```

#### Применение настроек:

Выполните скрипт инициализации, окружение "dev". При этом изменённые настройки будут скопированы в соответствующие папки

```
./init
```


Установка модуля управления пользователями [dektrium/yii2-user](https://github.com/dektrium/yii2-user/)
-----------------------

> Примечание: миграция будет приведена ниже

Для добавления зависимости выполните из папки приложения
```
composer require "dektrium/yii2-user:0.9.*@dev"
```

Добавьте в конфиги модуль `user`,
Уберите одноимённый компонент `user`, используемый в исходном шаблоне в конфигах
`yii2-advanced-rbac/backend/config/main.php` и `yii2-advanced-rbac/frontend/config/main.php`

Конфигурация `yii2-advanced-rbac/common/config/main.php`

```
    'modules' => [
        'user' => [
            'class' => 'dektrium\user\Module',
        ],
    ],
```

Запрет доступа к профилю, восстановлению пароля, регистрации и настройкам своего аккаунта из бекенда: в
`yii2-advanced-rbac/backend/config/main.php` добавить:

```
    'modules' => [
        'user' => [
            // Отключить контроллеры profile, recovery, registration, settings. Остались security, admin
            'as backend' => 'dektrium\user\filters\BackendFilter',
        ],
    ],
```

Запрет администрирования с фронтенда: в `yii2-advanced-rbac/frontend/config/main.php` добавить:

```
    'modules' => [
        'user' => [
            // Отключить контроллер admin. Остались profile, recovery, registration, security, settings.
            'as frontend' => 'dektrium\user\filters\FrontendFilter',
        ],
    ],
```

> Примечание: после установки модуля можно удалить стандартные средства работы с входом: модель, виды:
```
yii2-advanced-rbac/backend/views/site/login.php
yii2-advanced-rbac/common/models/LoginForm.php
yii2-advanced-rbac/common/models/User.php
yii2-advanced-rbac/frontend/views/site/login.php
yii2-advanced-rbac/frontend/views/site/requestPasswordResetToken.php
yii2-advanced-rbac/frontend/views/site/resetPassword.php
yii2-advanced-rbac/frontend/views/site/signup.php
```
И изменить ссылки в видах-слоях
```
yii2-advanced-rbac/backend/views/layouts/main.php
yii2-advanced-rbac/frontend/views/layouts/main.php
```

Про использование модуля узнайте в соответствующем [разделе](start-modules.md) или в
[официальной документации](https://github.com/dektrium/yii2-user/blob/master/docs/README.md)


Установка модуля [githubjeka/gui-rbac-yii2](https://github.com/githubjeka/gui-rbac-yii2)
-----------------------

Для добавления зависимости выполните из папки приложения
```
composer require "githubjeka/yii2-gui-rbac:*"
```

Добавьте в конфиг бэкенда `yii2-advanced-rbac/backend/config/main.php` модуль `rbac`,
```
'modules' => [
    'rbac' => [
        'class' => 'githubjeka\rbac\Module',
    ],
],
```

Про использование модуля узнайте в соответствующем [разделе](start-modules.md) или в
[официальной документации](https://github.com/githubjeka/gui-rbac-yii2/blob/master/README.md)


Установка модуля управления RBAC [mdmsoft/yii2-admin](https://github.com/mdmsoft/yii2-admin)
-----------------------

> Примечание: миграция будет приведена ниже

Для добавления зависимости выполните из папки приложения
```
composer require "mdmsoft/yii2-admin:2.x-dev"
```

Добавьте в общий конфиг `yii2-advanced-rbac/common/config/main.php` модуль `admin`,
```
'modules' => [
    'admin' => [
        'class' => 'mdm\admin\Module',
    ]
],
```

Про использование модуля узнайте в соответствующем [разделе](start-modules.md) или в
[официальной документации](https://github.com/mdmsoft/yii2-admin/blob/master/README.md)


Применение миграций, стартовое администрирование, установка разрешений на действия и контроллеры
-----------------------

Добавьте конфигурацию компонента приложения `rbac` в общий конфиг `yii2-advanced-rbac/common/config/main.php`
```
    'authManager' => [
        'class' => 'yii\rbac\DbManager',
    ],
```

Ниже приведён список источников миграций, которые были использованы в требуемом порядке.
При установке модулей по отдельности необходимо использовать соответствуюие пути миграции.
Эти же пути должны быть указаны для отмены миграции:

```
./yii migrate/up --migrationPath=@yii/rbac/migrations/
./yii migrate/up --migrationPath=@dektrium/user/migrations
./yii migrate/up --migrationPath=@mdm/admin/migrations
```

Выполните миграции
```
./yii migrate/up
```

При этом вы проведёте инициализацию rbac. **Первый пользователь получит права администратора**.

Создать пользователя можно тут же, командой
```
 ./yii user/create usermail@usermailserver.com login
```

Вы успешно установили три модуля и инициализировали RBAC, теперь самое время использовать эту систему для ограничения доступа.

Сделать это можно через конфиг, добавляя поведение либо к контролеру, либо к целевому модулю либо к приложению вообще

```
    'modules' => [
        'admin' => [
            'class' => 'mdm\admin\Module',
            'as access' => [
                'class' => 'yii\filters\AccessControl',
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['administrateRbac'],
                    ],
                ],
            ],
        ],
    ],
```

Для контроллеров модуля - используя карту контроллеров
```
    'modules' => [
        'user' => [
            'class' => 'dektrium\user\Module',
            'controllerMap' => [
                'admin' => [
                    'class' => 'dektrium\user\controllers\AdminController',
                    'as access' => [
                        'class' => 'yii\filters\AccessControl',
                        'rules' => [
                            [
                                'allow' => true,
                                'roles' => ['administrateUser'],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
```

В любом месте - используя конструкцию
```
yii\web\User::can()
```