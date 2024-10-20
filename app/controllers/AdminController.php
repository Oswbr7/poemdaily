<?php

use Phalcon\Mvc\Controller;

//Add plugin
include __DIR__ . "/../plugings/simple_html_dom.php";

class AdminController extends Controller
{
    public function indexAction()
    {
        $this->view->login_url = $this->url->get('login');
    }

    public function dashboardAction()
    {
        $this->view->login_url = $this->url->get('login');

        $this->view->get_happy_poems_url = $this->url->get('admin/dashboard/getHappyPoems');
        $this->view->get_love_poems_url = $this->url->get('admin/dashboard/getLovePoems');
        $this->view->get_heart_break_url = $this->url->get('admin/dashboard/getHeartBreak');
        $this->view->get_distress_url = $this->url->get('admin/dashboard/getDistressPoems');
        $this->view->get_friend_ship_url = $this->url->get('admin/dashboard/getFriendShipPoems');
        $this->view->get_absence_poems_url = $this->url->get('admin/dashboard/getAbsencePoems');
        $this->view->get_desolation_poems_url = $this->url->get('admin/dashboard/getDesolationPoems');

        $this->view->happy_poems_url = HappyPoem::find();
        $this->view->love_poems_url = LovePoem::find();
        $this->view->heart_break_url = HeartBreak::find();
        $this->view->distrees_url = DistressPoem::find();
        $this->view->friend_ship_url = FriendShipPoem::find();
        $this->view->absence_url = AbsencePoem::find();
        $this->view->desolation_url = DesolationPoem::find();
    }

    public function getLovePoemsAction()
    {
        $not_save_poems = [];
        $save_poems = 0;
        $web_url = 'https://www.poesiacastellana.es/tematica-poemas.php?id=Amor';

        $html = file_get_html($web_url);

        if ($html) {
            $container_body = $html->find('#newspaper-b', 0);
            if ($container_body) {
                foreach ($container_body->find('tr') as $row) {
                    $first_td = $row->find('td', 0);
                    if ($first_td) {
                        $a_tag = $first_td->find('a', 0);
                        if ($a_tag) {
                            $href = $a_tag->href;
                            $poem_url = 'https://www.poesiacastellana.es/' . $href;

                            $html_poem = file_get_html($poem_url);

                            if ($html_poem) {
                                $poem_title = $html_poem->find('#titulo', 0);
                                $poem_content = $html_poem->find('#poema', 0);
                                $poem_author = $html_poem->find('#poeta', 0);

                                if ($poem_title && $poem_content && $poem_author) {
                                    $title = $poem_title->plaintext;
                                    $content = $poem_content->plaintext;
                                    $author = $poem_author->plaintext;

                                    $love_poem = new LovePoem();

                                    $love_poem->author = $author;
                                    $love_poem->title = $title;
                                    $love_poem->content = $content;
                                    $love_poem->created_at = date('Y-m-d H:i:s');

                                    if ($love_poem->save()) {
                                        $save_poems++;
                                    } else {
                                        $not_save_poems[] = 'No se pudo guardar el poema: ' . $title;
                                    }
                                } else {
                                    $not_save_poems[] = 'Faltan datos en el poema de la URL: ' . $poem_url;
                                }
                            } else {
                                $not_save_poems[] = 'No se pudo obtener el contenido de la página del poema: ' . $poem_url;
                            }
                        }
                    }
                }

                return $this->response->redirect('admin/dashboard?' . http_build_query([
                        'saved' => $save_poems,
                        'not_saved' => count($not_save_poems),
                        'errors' => $not_save_poems
                    ]));
            } else {
                return $this->response->redirect('admin/dashboard?' . http_build_query([
                        'error' => 'No se encontró la tabla con id newspaper-b'
                    ]));
            }
        } else {
            return $this->response->redirect('admin/dashboard?' . http_build_query([
                    'error' => 'No hubo conexión'
                ]));
        }
    }

    public function getHappyPoemsAction()
    {
        $not_save_poems = [];
        $save_poems = 0;
        $web_url = 'https://www.poesiacastellana.es/tematica-poemas.php?id=Alegr%C3%ADa';

        $html = file_get_html($web_url);

        if ($html) {
            $container_body = $html->find('#newspaper-b', 0);
            if ($container_body) {
                foreach ($container_body->find('tr') as $row) {
                    $first_td = $row->find('td', 0);
                    if ($first_td) {
                        $a_tag = $first_td->find('a', 0);
                        if ($a_tag) {
                            $href = $a_tag->href;
                            $poem_url = 'https://www.poesiacastellana.es/' . $href;

                            $html_poem = file_get_html($poem_url);

                            if ($html_poem) {
                                $poem_title = $html_poem->find('#titulo', 0);
                                $poem_content = $html_poem->find('#poema', 0);
                                $poem_author = $html_poem->find('#poeta', 0);

                                if ($poem_title && $poem_content && $poem_author) {
                                    $title = $poem_title->plaintext;
                                    $content = $poem_content->plaintext;
                                    $author = $poem_author->plaintext;

                                    $happy_poem = new HappyPoem();

                                    $happy_poem->author = $author;
                                    $happy_poem->title = $title;
                                    $happy_poem->content = $content;
                                    $happy_poem->created_at = date('Y-m-d H:i:s');

                                    if ($happy_poem->save()) {
                                        $save_poems++;
                                    } else {
                                        $not_save_poems[] = 'No se pudo guardar el poema: ' . $title;
                                    }
                                } else {
                                    $not_save_poems[] = 'Faltan datos en el poema de la URL: ' . $poem_url;
                                }
                            } else {
                                $not_save_poems[] = 'No se pudo obtener el contenido de la página del poema: ' . $poem_url;
                            }
                        }
                    }
                }

                return $this->response->redirect('admin/dashboard?' . http_build_query([
                        'saved' => $save_poems,
                        'not_saved' => count($not_save_poems),
                        'errors' => $not_save_poems
                    ]));
            } else {
                return $this->response->redirect('admin/dashboard?' . http_build_query([
                        'error' => 'No se encontró la tabla con id newspaper-b'
                    ]));
            }
        } else {
            return $this->response->redirect('admin/dashboard?' . http_build_query([
                    'error' => 'No hubo conexión'
                ]));
        }
    }

    public function getHeartBreakAction()
    {
        $not_save_poems = [];
        $save_poems = 0;
        $web_url = 'https://www.poesiacastellana.es/tematica-poemas.php?id=Desamor';

        $html = file_get_html($web_url);

        if ($html) {
            $container_body = $html->find('#newspaper-b', 0);
            if ($container_body) {
                foreach ($container_body->find('tr') as $row) {
                    $first_td = $row->find('td', 0);
                    if ($first_td) {
                        $a_tag = $first_td->find('a', 0);
                        if ($a_tag) {
                            $href = $a_tag->href;
                            $poem_url = 'https://www.poesiacastellana.es/' . $href;

                            $html_poem = file_get_html($poem_url);

                            if ($html_poem) {
                                $poem_title = $html_poem->find('#titulo', 0);
                                $poem_content = $html_poem->find('#poema', 0);
                                $poem_author = $html_poem->find('#poeta', 0);

                                if ($poem_title && $poem_content && $poem_author) {
                                    $title = $poem_title->plaintext;
                                    $content = $poem_content->plaintext;
                                    $author = $poem_author->plaintext;

                                    $heart_break = new HeartBreak();

                                    $heart_break->author = $author;
                                    $heart_break->title = $title;
                                    $heart_break->content = $content;
                                    $heart_break->created_at = date('Y-m-d H:i:s');

                                    if ($heart_break->save()) {
                                        $save_poems++;
                                    } else {
                                        $not_save_poems[] = 'No se pudo guardar el poema: ' . $title;
                                    }
                                } else {
                                    $not_save_poems[] = 'Faltan datos en el poema de la URL: ' . $poem_url;
                                }
                            } else {
                                $not_save_poems[] = 'No se pudo obtener el contenido de la página del poema: ' . $poem_url;
                            }
                        }
                    }
                }

                return $this->response->redirect('admin/dashboard?' . http_build_query([
                        'saved' => $save_poems,
                        'not_saved' => count($not_save_poems),
                        'errors' => $not_save_poems
                    ]));
            } else {
                return $this->response->redirect('admin/dashboard?' . http_build_query([
                        'error' => 'No se encontró la tabla con id newspaper-b'
                    ]));
            }
        } else {
            return $this->response->redirect('admin/dashboard?' . http_build_query([
                    'error' => 'No hubo conexión'
                ]));
        }
    }

    public function getDistressPoemsAction()
    {
        $not_save_poems = [];
        $save_poems = 0;
        $web_url = 'https://www.poesiacastellana.es/tematica-poemas.php?id=Angustia';

        $html = file_get_html($web_url);

        if ($html) {
            $container_body = $html->find('#newspaper-b', 0);
            if ($container_body) {
                foreach ($container_body->find('tr') as $row) {
                    $first_td = $row->find('td', 0);
                    if ($first_td) {
                        $a_tag = $first_td->find('a', 0);
                        if ($a_tag) {
                            $href = $a_tag->href;
                            $poem_url = 'https://www.poesiacastellana.es/' . $href;

                            $html_poem = file_get_html($poem_url);

                            if ($html_poem) {
                                $poem_title = $html_poem->find('#titulo', 0);
                                $poem_content = $html_poem->find('#poema', 0);
                                $poem_author = $html_poem->find('#poeta', 0);

                                if ($poem_title && $poem_content && $poem_author) {
                                    $title = $poem_title->plaintext;
                                    $content = $poem_content->plaintext;
                                    $author = $poem_author->plaintext;

                                    $distress = new DistressPoem();

                                    $distress->author = $author;
                                    $distress->title = $title;
                                    $distress->content = $content;
                                    $distress->created_at = date('Y-m-d H:i:s');

                                    if ($distress->save()) {
                                        $save_poems++;
                                    } else {
                                        $not_save_poems[] = 'No se pudo guardar el poema: ' . $title;
                                    }
                                } else {
                                    $not_save_poems[] = 'Faltan datos en el poema de la URL: ' . $poem_url;
                                }
                            } else {
                                $not_save_poems[] = 'No se pudo obtener el contenido de la página del poema: ' . $poem_url;
                            }
                        }
                    }
                }

                return $this->response->redirect('admin/dashboard?' . http_build_query([
                        'saved' => $save_poems,
                        'not_saved' => count($not_save_poems),
                        'errors' => $not_save_poems
                    ]));
            } else {
                return $this->response->redirect('admin/dashboard?' . http_build_query([
                        'error' => 'No se encontró la tabla con id newspaper-b'
                    ]));
            }
        } else {
            return $this->response->redirect('admin/dashboard?' . http_build_query([
                    'error' => 'No hubo conexión'
                ]));
        }
    }

    public function getFriendShipPoemsAction()
    {
        $not_save_poems = [];
        $save_poems = 0;
        $web_url = 'https://www.poesiacastellana.es/tematica-poemas.php?id=Amistad';

        $html = file_get_html($web_url);

        if ($html) {
            $container_body = $html->find('#newspaper-b', 0);
            if ($container_body) {
                foreach ($container_body->find('tr') as $row) {
                    $first_td = $row->find('td', 0);
                    if ($first_td) {
                        $a_tag = $first_td->find('a', 0);
                        if ($a_tag) {
                            $href = $a_tag->href;
                            $poem_url = 'https://www.poesiacastellana.es/' . $href;

                            $html_poem = file_get_html($poem_url);

                            if ($html_poem) {
                                $poem_title = $html_poem->find('#titulo', 0);
                                $poem_content = $html_poem->find('#poema', 0);
                                $poem_author = $html_poem->find('#poeta', 0);

                                if ($poem_title && $poem_content && $poem_author) {
                                    $title = $poem_title->plaintext;
                                    $content = $poem_content->plaintext;
                                    $author = $poem_author->plaintext;

                                    $friend_ship = new FriendShipPoem();

                                    $friend_ship->author = $author;
                                    $friend_ship->title = $title;
                                    $friend_ship->content = $content;
                                    $friend_ship->created_at = date('Y-m-d H:i:s');

                                    if ($friend_ship->save()) {
                                        $save_poems++;
                                    } else {
                                        $not_save_poems[] = 'No se pudo guardar el poema: ' . $title;
                                    }
                                } else {
                                    $not_save_poems[] = 'Faltan datos en el poema de la URL: ' . $poem_url;
                                }
                            } else {
                                $not_save_poems[] = 'No se pudo obtener el contenido de la página del poema: ' . $poem_url;
                            }
                        }
                    }
                }

                return $this->response->redirect('admin/dashboard?' . http_build_query([
                        'saved' => $save_poems,
                        'not_saved' => count($not_save_poems),
                        'errors' => $not_save_poems
                    ]));
            } else {
                return $this->response->redirect('admin/dashboard?' . http_build_query([
                        'error' => 'No se encontró la tabla con id newspaper-b'
                    ]));
            }
        } else {
            return $this->response->redirect('admin/dashboard?' . http_build_query([
                    'error' => 'No hubo conexión'
                ]));
        }
    }

    public function getAbsencePoemsAction()
    {
        $not_save_poems = [];
        $save_poems = 0;
        $web_url = 'https://www.poesiacastellana.es/tematica-poemas.php?id=Ausencia';

        $html = file_get_html($web_url);

        if ($html) {
            $container_body = $html->find('#newspaper-b', 0);
            if ($container_body) {
                foreach ($container_body->find('tr') as $row) {
                    $first_td = $row->find('td', 0);
                    if ($first_td) {
                        $a_tag = $first_td->find('a', 0);
                        if ($a_tag) {
                            $href = $a_tag->href;
                            $poem_url = 'https://www.poesiacastellana.es/' . $href;

                            $html_poem = file_get_html($poem_url);

                            if ($html_poem) {
                                $poem_title = $html_poem->find('#titulo', 0);
                                $poem_content = $html_poem->find('#poema', 0);
                                $poem_author = $html_poem->find('#poeta', 0);

                                if ($poem_title && $poem_content && $poem_author) {
                                    $title = $poem_title->plaintext;
                                    $content = $poem_content->plaintext;
                                    $author = $poem_author->plaintext;

                                    $absence = new AbsencePoem();

                                    $absence->author = $author;
                                    $absence->title = $title;
                                    $absence->content = $content;
                                    $absence->created_at = date('Y-m-d H:i:s');

                                    if ($absence->save()) {
                                        $save_poems++;
                                    } else {
                                        $not_save_poems[] = 'No se pudo guardar el poema: ' . $title;
                                    }
                                } else {
                                    $not_save_poems[] = 'Faltan datos en el poema de la URL: ' . $poem_url;
                                }
                            } else {
                                $not_save_poems[] = 'No se pudo obtener el contenido de la página del poema: ' . $poem_url;
                            }
                        }
                    }
                }

                return $this->response->redirect('admin/dashboard?' . http_build_query([
                        'saved' => $save_poems,
                        'not_saved' => count($not_save_poems),
                        'errors' => $not_save_poems
                    ]));
            } else {
                return $this->response->redirect('admin/dashboard?' . http_build_query([
                        'error' => 'No se encontró la tabla con id newspaper-b'
                    ]));
            }
        } else {
            return $this->response->redirect('admin/dashboard?' . http_build_query([
                    'error' => 'No hubo conexión'
                ]));
        }
    }

    public function getDesolationPoemsAction()
    {
        $not_save_poems = [];
        $save_poems = 0;
        $web_url = 'https://www.poesiacastellana.es/tematica-poemas.php?id=Desolación';

        $html = file_get_html($web_url);

        if ($html) {
            $container_body = $html->find('#newspaper-b', 0);
            if ($container_body) {
                foreach ($container_body->find('tr') as $row) {
                    $first_td = $row->find('td', 0);
                    if ($first_td) {
                        $a_tag = $first_td->find('a', 0);
                        if ($a_tag) {
                            $href = $a_tag->href;
                            $poem_url = 'https://www.poesiacastellana.es/' . $href;

                            $html_poem = file_get_html($poem_url);

                            if ($html_poem) {
                                $poem_title = $html_poem->find('#titulo', 0);
                                $poem_content = $html_poem->find('#poema', 0);
                                $poem_author = $html_poem->find('#poeta', 0);

                                if ($poem_title && $poem_content && $poem_author) {
                                    $title = $poem_title->plaintext;
                                    $content = $poem_content->plaintext;
                                    $author = $poem_author->plaintext;

                                    $desolation = new DesolationPoem();

                                    $desolation->author = $author;
                                    $desolation->title = $title;
                                    $desolation->content = $content;
                                    $desolation->created_at = date('Y-m-d H:i:s');

                                    if ($desolation->save()) {
                                        $save_poems++;
                                    } else {
                                        $not_save_poems[] = 'No se pudo guardar el poema: ' . $title;
                                    }
                                } else {
                                    $not_save_poems[] = 'Faltan datos en el poema de la URL: ' . $poem_url;
                                }
                            } else {
                                $not_save_poems[] = 'No se pudo obtener el contenido de la página del poema: ' . $poem_url;
                            }
                        }
                    }
                }

                return $this->response->redirect('admin/dashboard?' . http_build_query([
                        'saved' => $save_poems,
                        'not_saved' => count($not_save_poems),
                        'errors' => $not_save_poems
                    ]));
            } else {
                return $this->response->redirect('admin/dashboard?' . http_build_query([
                        'error' => 'No se encontró la tabla con id newspaper-b'
                    ]));
            }
        } else {
            return $this->response->redirect('admin/dashboard?' . http_build_query([
                    'error' => 'No hubo conexión'
                ]));
        }
    }
}

