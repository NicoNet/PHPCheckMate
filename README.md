PHPcheckmate
============

phpcheckmate is a chess framework written in php. Providing moves validation and game recording allowing us to tack pieces back. The project is object oriented and contains several classes. phpcheckmate is a php portage of the piece of code written in Perl by Brian Richardson.  This library originally by David Soyez.


``` php
{{{
$game = new Game();
$game->make_move("e2", "e3");
$game->make_move("f7", "f6");
$game->make_move("d2", "d3");
$game->make_move("g7", "g5");
$game->make_move("d1", "h5");
var_dump($game->player_checkmated("black")); //true #Player2 checkmated
var_dump($game->result()); //1 #Game is over

$game->take_back_move(); #Take back last move

var_dump($game->player_checkmated("black")); //false #Player2 NOT checkmated
var_dump($game->result()); // false #Player2 can still move its king
$game->make_move("d1", "h5");
var_dump($game->player_checkmated("black")); //true #Player2 checkmated




$game = new Game();
$game->make_move("e2", "e4");
$game->make_move("e7", "e6");
$game->make_move("d2", "d4");
$game->make_move("f7", "f6");
$game->make_move("c1", "h6");
$game->make_move("g7", "h6");
$game->make_move("d1", "h5");
var_dump($game->player_checkmated("black")); //false #Player2 NOT checkmated
}}}
```
