Бандл лайков для Symfony 2
==========================

Установка
---------

Установка через композер, добавляем в composer.json
> "undelete/likes-bundle": "dev-master"

Регестрируем бандл в AppKernel
``` php
new Undelete\LikesBundle\UndeleteLikesBundle(),
```

Конфигурмруем
``` yaml
# app/config/config.yml

undelete_likes:
    user: Acme\DemoBundle\Entity\User
```

Использование
-------------

Для подключении лайков к классу нужно:
* создать переменную $likes с привязкой ManyToMany к Undelete\LikesBundle\Entity\Like
* имлементировать Undelete\LikesBundle\Entity\LikeableInterface
* указать класс в конфиге бандла лайков

Указываем классы в конфиге
``` yaml
# app/config/config.yml

undelete_likes:
    ...
    classes:
        article: Acme\DemoBundle\Entity\Article
```
