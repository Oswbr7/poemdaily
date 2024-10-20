<?php

use Phalcon\Mvc\Controller;
use Phalcon\Session\Adapter\Files as Session;
use Phalcon\Di\Injectable;

$di->setShared(
    'session',
    function () {
        $session = new Session();
        $session->start();
        return $session;
    }
);

class User
{
    public $id;
    public $likes;

    public function __construct(int $id, array $likes)
    {
        $this->id = $id;
        $this->likes = $likes;
    }
}

class RecommendationSystem extends Injectable
{
    public function fetchUserPreferences(int $userId): User
    {
        $user = Users::findFirst($userId);

        $likes = [
            'amor' => (int)$user->like_amor,
            'anoranza' => (int)$user->like_anoranza,
            'ausencia' => (int)$user->like_ausencia,
            'desamor' => (int)$user->like_desamor,
            'angustia' => (int)$user->like_angustia,
            'amistad' => (int)$user->like_amistad,
            'desolacion' => (int)$user->like_desolacion,
        ];

        return new User($userId, $likes);
    }

    private function calculateDistance(User $userX, User $user): float
    {
        $distance = 0;
        $userLikes = $this->fetchLikes($userX->id);
        $otherLikes = $this->fetchLikes($user->id);
        foreach ($userLikes as $genre => $poemID) {
            if (!isset($otherLikes[$genre]))
                $distance++;
            elseif (!in_array($poemID, $otherLikes[$genre]))
                $distance++;
        }
        return $distance;
    }

    public function fetchLikes(int $userID)
    {
        $userLikes = [];
        $results = $this->db->query("SELECT poemID, genre FROM poemlikes WHERE userID = {$userID}")->fetchAll();
        foreach ($results as $row) {
            if (!isset($userLikes[$row['genre']]))
                $userLikes[$row['genre']] = [];
            array_push($userLikes[$row['genre']], $row['poemID']);
        }
        return $userLikes;
    }

    public function findNearestNeighbors(User $userX, int $k): array
    {
        $users = Users::find([
            'conditions' => 'id != :curr:',
            'bind' => ['curr' => $userX->id],
        ]);
        $distances = [];
        foreach ($users as $user) {
            $userPrefs = $this->fetchUserPreferences((int)$user->id);
            $distance = $this->calculateDistance($userX, $userPrefs);
            $distances[] = ['user' => $userPrefs, 'distance' => $distance];
        }
        usort($distances, function ($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });
        return array_slice($distances, 0, $k);
    }

    public function recommendPoems(User $userX, int $k): array
    {
        $neighbors = $this->findNearestNeighbors($userX, $k);
        $recommendedPoems = [];
        $finalRecommendations = [];
        foreach ($neighbors as $neighbor) {
            # Poner variables directamente en queries está mal pero si no no funcionaba.
            # No hay forma de hacer inyecciones aquí, los valores se obtienen de la DB
            $results = $this->db->query("SELECT poemID, genre FROM poemlikes WHERE userID = {$neighbor['user']->id} AND CONCAT(poemID, genre) NOT IN (SELECT CONCAT(poemID, genre) FROM poemlikes WHERE userID = {$userX->id})")->fetchAll();
            foreach ($results as $row) {
                $key = $row['poemID'] . '|' . $row['genre'];
                if (!isset($recommendedPoems[$key]))
                    $recommendedPoems[$key] = 0;
                $recommendedPoems[$key]++;
            }
        }
        arsort($recommendedPoems);
        foreach ($recommendedPoems as $key => $count) {
            [$poemID, $genre] = explode('|', $key);
            array_push($finalRecommendations, ['poemID' => $poemID, 'genre' => $genre]);
        }
        return $finalRecommendations;
    }
}

class ClientController extends Controller
{
    public function indexAction()
    {
        $this->view->login_url = $this->url->get('login');
        $this->view->poems_url = $this->url->get('poems');
        $this->view->logout_url = $this->url->get('client_logout');
    }

    public function randomAction()
    {
        // Lista de modelos de poemas
        $models = [
            "amor" => LovePoem::class,
            "amistad" => FriendShipPoem::class,
            "ausencia" => AbsencePoem::class,
            "anoranza" => HappyPoem::class,
            "desolacion" => DesolationPoem::class,
            "angustia" => DistressPoem::class,
            "desamor" => HeartBreak::class
        ];
        //Elige un modelo aleatorio
        $randomModel = $models[array_rand($models)];
        // Obtener un poema aleatorio del modelo elegido
        $poem = $randomModel::findFirst([
            'order' => 'RAND()'  // RAND() para MySQL
        ]);
        $this->view->pick('client/dashboard');
        $this->view->mode = 'POEMA ALEATORIO';
        $this->view->poem = $poem;

        $this->view->logout_url = $this->url->get('client_logout');
    }

    public function favviewAction()
    {
        $this->view->pick('client/dashboard');
        $models = [
            "amor" => LovePoem::class,
            "amistad" => FriendShipPoem::class,
            "ausencia" => AbsencePoem::class,
            "anoranza" => HappyPoem::class,
            "desolacion" => DesolationPoem::class,
            "angustia" => DistressPoem::class,
            "desamor" => HeartBreak::class
        ];
        $favPoem = Favorites::findFirst([
            'conditions' => 'userID = :userID:',
            'bind'       => [
                'userID' => $this->session->get('userId')
            ],
            'order'      => 'RAND()'
        ]);

        $this->view->mode = 'TUS POEMAS FAVORITOS';
        if (isset($favPoem->genre) && $models[$favPoem->genre]::findFirst("id = {$favPoem->poemID}"))
            $this->view->poem = $models[$favPoem->genre]::findFirst("id = {$favPoem->poemID}");
        else {
            $this->view->poem = (object)[
                'title' => '¡No tienes favoritos registrados!',
                'content' => "<button class='btn text-center btn-success' type='submit' onclick='window.location=\"/poemdaily/client/random\"'> Haz click aquí para empezar a ver poemas </button>",
                'explication' => "",
            ];
            $this->view->hide = true;
        }

        $this->view->logout_url = $this->url->get('client_logout');
    }

    public function dashboardAction(int $k = 3, bool $randomness = FALSE)
    {
        $models = [
            "amor" => LovePoem::class,
            "amistad" => FriendShipPoem::class,
            "ausencia" => AbsencePoem::class,
            "anoranza" => HappyPoem::class,
            "desolacion" => DesolationPoem::class,
            "angustia" => DistressPoem::class,
            "desamor" => HeartBreak::class
        ];
        $recommendation = $this->recommend($this->session->get('userId'), $k, $randomness);
        $this->view->mode = 'RECOMENDACIÓN PARA TI';
        if (isset($recommendation['genre']) && $models[$recommendation['genre']]::findFirst("id = {$recommendation['poemID']}"))
            $this->view->poem = $models[$recommendation['genre']]::findFirst("id = {$recommendation['poemID']}");
        else {
            $this->view->poem = (object)[
                'title' => '¡No hay recomendaciones por el momento!',
                'content' => "<button class='btn text-center btn-success' type='submit' onclick='window.location=\"/poemdaily/client/random\"'> Haz click aquí para ver poemas </button>",
                'explication' => "",
            ];
            $this->view->hide = true;
        }

        $this->view->logout_url = $this->url->get('client_logout');
    }

    public function recommend(int $userId, int $k, bool $randomness)
    {
        $recommendationSystem = new RecommendationSystem();
        $userX = $recommendationSystem->fetchUserPreferences($userId);
        $recommendations = $recommendationSystem->recommendPoems($userX, $k);
        if (count($recommendations) > 0) {
            if ($randomness)
                return $recommendations[rand(0, min($k, count($recommendations)) - 1)];
            else
                return $recommendations[0];
        } else
            return null;
    }

    public function likeAction($genre, $poemID)
    {
        $like = new PoemLikes();
        $like->userID = $this->session->get('userId');
        $like->poemID = $poemID;
        $like->genre = $genre;
        if ($like->save() === false)
            foreach ($like->getMessages() as $message)
                echo $message;
        $user = Users::findFirst("id = {$this->session->get('userId')}");
        switch ($genre) {
            case 'amor':
                $user->like_amor = $user->like_amor + 1 ?? 1;
                break;
            case 'anoranza':
                $user->like_anoranza = $user->like_anoranza + 1 ?? 1;
                break;
            case 'ausencia':
                $user->like_ausencia = $user->like_ausencia + 1 ?? 1;
                break;
            case 'desamor':
                $user->like_desamor = $user->like_desamor + 1 ?? 1;
                break;
            case 'angustia':
                $user->like_angustia = $user->like_angustia + 1 ?? 1;
                break;
            case 'amistad':
                $user->like_amistad = $user->like_amistad + 1 ?? 1;
                break;
            case 'desolacion':
                $user->like_desolacion = $user->like_desolacion + 1 ?? 1;
                break;
        }
        if ($user->save() === false)
            foreach ($user->getMessages() as $message)
                echo $message;
    }

    public function favoriteAction($genre, $poemID)
    {
        $fav = new Favorites();
        $fav->userID = $this->session->get('userId');
        $fav->poemID = $poemID;
        $fav->genre = $genre;
        if ($fav->save() === false)
            foreach ($fav->getMessages() as $message)
                echo $message;
    }

    public function logOutAction()
    {
        $this->session->destroy();

        return $this->response->redirect('login');
    }

}