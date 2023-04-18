# Настройка MySQL, PHP, Nginx

## Подсказки:

### Запуск контейнера:
`docker-compose up`

### Запуск контейнера в фоновом режиме:
`docker-compose up -d`

### Просмотр запущенных контейнеров:
`docker-compose ps`

### Остановка запущенных контейнеров:
`docker-compose down`

### Запуск ‘bash’ в контейнере:
`docker exec -t -i имя_контейнера /bin/bash`

### Запуск mysql клиента
`mysql -u пользователь -p`

### Обновление репозитория, сохраняя локальные изменения
1. `git stash`
2. `git pull`
3. `git stash pop`

## Настройка docker контейнеров
1. Склонировать репозиторий:

    `git clone https://github.com/vozakharova/ktbd.git`
2. В файле *'docker-compose.yml'* в строке 16 вместо звездочки '*' подставить расположение склонированной директории:

    Было: `'*/volume/mysql:/var/lib/mysql'`

    Стало: `$HOME/ktbd/volume/mysql:/var/lib/mysql`
3. Сохранить файл *'docker-compose.yml'*
4. Запустить сборку (находясь в директории *'ktbd'*):

    `docker-compose build`
5. Запустить контейнеры (находясь в директории *'ktbd'*):

    `docker-compose up -d`
6. Если сборка была настроена правильно, то при переходе в браузере на *'localhost'* будет отображено:

    **`It works!`**
