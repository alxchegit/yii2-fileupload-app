## Тестовое задание для PHP (с уклоном в фуллстек) разработчика

Настроить веб-приложение, суть которого в следующем:
1. Одна страница, на которой пользователь имеет возможность залогиниться
2. Для залогиненного юзера, становится доступна другая страница с формой загрузки файлов, и списком ранее загруженных файлов.
3. Загружать файлы нужно на какой-нибудь сторонний хостинг (dropbox, google/yandex drive, youtube, vimeo или любой другой) - выбрать один по желанию.
4. В списке файлов отображать Название файла, Дату загрузки и ссылку на хостинг (ссылку для скачивания, или на страницу просмотра - если речь о медиа-хостинге)
5. У залогиненного пользователя должна быть возможность разлогиниться.
6. Каждому пользователю доступен только свой список файлов
7. Опционально фильтрация и сортировка этого списка

## Комментарии:
Нужно использовать любой php-фреймворк, на свой вкус (symfony, zend, laravel и т.д. и т.п.). Юзеров и инфу по файлам, хранить в БД (в любой реляционной, mysql/postrgsql  и т.д. и т.п.). Использовать докер необязательно, но желательно (это сильно упростит запуск и просмотр тестового задания). По фронтовой части: очень желательно, чтобы данные (прежде всего список файлов), формируемые на беке, передавались в качестве JSON объекта на клиент. А рендер уже осуществлялся на клиенте, по средствам JS. Можно реализовать как на native js (es6), так и использовать какой-нибудь js-фреймворк, но это по желанию.

## Что мы НЕ хотим увидеть: 
Простыню в один index.php, с захардкоженымм подключением к базе, там же выполняемыми запросами, описанными вьюхами, вставками html и js. 
