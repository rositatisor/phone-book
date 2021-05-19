![Products](https://img.shields.io/badge/PHP-application-blue)

# Phone-book
**Description:** Baltic Amadeus task for Junior PHP Developer position: Create Phone book application using PHP Symfony framework.

## Application
**Result:** Fully functioning phone book.

**Requirements:**
- [x] entries of the phone book are accessible only to the registered users;
- [x] there is an option to share entry with other registered user;
- [x] the phone book entry consists of the following: name, phone (full CRUD), additionally added two peoperties - user_id and favourite;
- [x] there must be at least one unit test;
- [x] there must be at least one feature test;
- [ ] application must be Dockerized*;
- [x] optional: application accessible via GUI or/and API, application uses only GUI;
- [x] other - added the new feature: the ability to add contacts to the favourites;

\* The application is not Dockerized regarding some issues faced when building images due to following errors:
- net/http request canceled while waiting for connection (Client.Timeout exceeded while awaiting headers);
- net/http: TLS handshake timeout.

_However, due to the reasons the application is not using Docker, for easy access purposes the app with development environment is hosted and can be accessed via following link: [https://narkute.lt/phone-book/public/](https://narkute.lt/phone-book/public/)._

## Tests
There are tests available for the application in the /tests directory.
In the terminal in the project directory, all tests can be run with one command:
```
./vendor/bin/phpunit
```

## Preview
<img width="700" alt="Capture" src="https://user-images.githubusercontent.com/70884246/118752568-fec0ed00-b86b-11eb-9ef2-86f3eb1cea1b.png">

## Author

[Rosita](https://github.com/rositatisor)
