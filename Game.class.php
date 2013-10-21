<?php 
/**
 *  PHPCheckMate
 * 
 *  @author David Soyez <phpgnu@0x404.net>
 *  @version 1.0
 *  @copyright Copyright (c) 2009-2010, David Soyez
 *  @license GNU General Public License Version 3 <http://www.gnu.org/licenses/>
 *  @package phpcheckmate
 *  
 *  PHPCheckMate is a portage of Chess written in Perl by Brian Richardson
 *  Original Perl source can be find at http://search.cpan.org/~bjr/
 *  
 *  PHPCheckMate is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  Foobar is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
 */

  chdir(dirname(__File__));
  include_once('Board.class.php');
  include_once('game/MoveList.class.php');
  include_once('Piece.class.php');
  include_once('piece/Bishop.class.php');
  include_once('piece/King.class.php');
  include_once('piece/Knight.class.php');
  include_once('piece/Pawn.class.php');
  include_once('piece/Queen.class.php');
  include_once('piece/Rook.class.php');

  
  class Game{
  
    const __MOVE_CAPTURE__		= 0x01;
  	const __MOVE_CASTLE_SHORT__	= 0x02;
  	const __MOVE_CASTLE_LONG__	= 0x04;
  	const __MOVE_EN_PASSANT__	= 0x08;
  	const __MOVE_PROMOTE__		= 0x10;
  	
  	public static $games		= array();
  	
  	public $board;
  	public $player_has_moves 	= array();
  	public $captures 			= array();
	public $kings 				= array();
	public $players			= array();
	public $pieces 			= array();
	public $movelist;
	public $message 			= '';
    
	
	
	
    /**
     * Takes two optional parameters containing optional names for the players. 
     * If none are provided, the player names 'white' and 'black' are used. 
     * Creates a new Board and places 16 Pieces per player and 
     * initializes an empty MoveList.
     * @param string $p1 Player1's name
     * @param string $p2 Player2's name
     * @return Game 
     */
	function __construct($p1 = '', $p2 = ''){
		#player names
        $playerName1    = $p1 ? $p1:'white';
        $playerName2    = $p2 ? $p2:'black';
        $player1        = 'white';
        $player2        = 'black';
    	
    	#create the board content
    	$this->board 			= new Board();
    	$this->pieces			= array($player1 => array(), $player2 => array());
    	$this->captures			= array($player1 => array(), $player2 => array());
    	$this->player_has_moves	= array($player1 => array(1=>1), $player2 => array(1=>1));
    	$this->movelist			= new MoveList($player1, $player2);
		$this->players			= array($player1, $player2);
    	
    	#Add Rooks, Knights, Bishops and Queens to the board
    	$this->add_pieces('Rook', array('a1','h1'), $player1);
    	$this->add_pieces('Rook', array('a8','h8'), $player2);
    	$this->add_pieces('Knight', array('b1','g1'), $player1);
    	$this->add_pieces('Knight', array('b8','g8'), $player2);
    	$this->add_pieces('Bishop', array('c1','f1'), $player1);
    	$this->add_pieces('Bishop', array('c8','f8'), $player2);
    	$this->add_pieces('Queen', array('d1'), $player1);
    	$this->add_pieces('Queen', array('d8'), $player2);
    	
    	#add kings to the board
    	$hash = $this->add_pieces('King', array('e1'), $player1);
    	$this->kings[]	= $this->pieces[$player1][$hash];
    	$hash = $this->add_pieces('King', array('e8'), $player2);    	
    	$this->kings[]	= $this->pieces[$player2][$hash];	

    	#add pawns to the board
    	$pawn_row = Board::squares_in_line("a2", "h2");
    	$this->add_pieces('Pawn', $pawn_row, $player1);
    	$pawn_row = Board::squares_in_line("a7", "h7");
    	$this->add_pieces('Pawn', $pawn_row, $player2);
    	
    	return $this;
    }
    
    
    
    
    /**
     * Returns a new Game reference in an identical state to the calling object, but which can be
     * manipulated entirely separately.
     * @return Game
     */
  	function __clone(){

  	$new_obj = new Game();
  	unset($new_obj->kings);
	$clone = clone $this->board;
	$p1 = $this->players[0];
	$p2 = $this->players[1];
	$pieces = array($p1 => array(), $p2 => array());
	$new_obj->pieces = $pieces;
	$captures = array($p1 => array(), $p2 => array());
	$new_obj->captures = $captures;
	$old_to_new = array();
	$new_obj->players = $this->players;
	$new_obj->player_has_moves = $this->player_has_moves;
	
	foreach($this->pieces[$p1] as $old_piece) {
	    if ($old_piece instanceof King || !$old_piece->captured()) {
			$old_sq = $old_piece->get_current_square();
    		$new_piece = $clone->get_piece_at($old_sq);
    		$old_to_new[spl_object_hash($old_piece)] = $new_piece;
    		$new_obj->pieces[$p1][] = $new_piece;
			if ($new_piece == true && $new_piece instanceof King)
				$new_obj->kings[] = $new_piece ;
	    }
	    else {
		foreach($this->captures[$p2] as $mn => $obj){
	    	    $capture = $this->captures[$p2][$mn];
		    if ($capture === $old_piece) {
		    	$clonedcapture = clone $capture;
    			$captures[$p1][$mn] = $clonedcapture;
				$old_to_new[spl_object_hash($old_piece)] = $clonedcapture;
				$new_obj->pieces[$p1][] = $clonedcapture;
		    }
		}
	    }
	}
	
  	foreach($this->pieces[$p2] as $old_piece) {
	    if ($old_piece instanceof King || !$old_piece->captured()) {
			$old_sq = $old_piece->get_current_square();
    		$new_piece = $clone->get_piece_at($old_sq);
    		$old_to_new[spl_object_hash($old_piece)] = $new_piece;
    		$new_obj->pieces[$p2][] = $new_piece;
			if ($new_piece == true && $new_piece instanceof King)
				$new_obj->kings[] = $new_piece ;
	    }
	    else {
		foreach($this->captures[$p1] as $mn => $obj){
	    	    $capture = $this->captures[$p1][$mn];
		    if ($capture === $old_piece) {
		    	$clonedcapture = clone $capture;
    			$captures[$p1][$mn] = $clonedcapture;
				$old_to_new[spl_object_hash($old_piece)] = $clonedcapture;
				$new_obj->pieces[$p2][] = $clonedcapture;
		    }
		}
	    }
	}
		

	$movelist = $this->movelist;
	$new_ml =  new MoveList($p1, $p2);
	list($p1_moves, $p2_moves) = $movelist->get_all_moves();
	for ($i = 0; $i < count($p1_moves); $i++) {
	    $p1_move = $p1_moves[$i];
	    $p2_move = $p2_moves[$i];
	    $piece = $old_to_new[spl_object_hash($p1_move->get_piece())];
	    $sq1 = $p1_move->get_start_square();
	    $sq2 = $p1_move->get_dest_square();
	    $flags = 0x0;
	    if ($p1_move->is_capture()) $flags |= self::__MOVE_CAPTURE__ ;
	    if ($p1_move->is_short_castle()) $flags |= self::__MOVE_CASTLE_SHORT__ ;
	    if ($p1_move->is_long_castle()) $flags |= self::__MOVE_CASTLE_LONG__ ;
	    if ($p1_move->is_en_passant()) $flags |= self::__MOVE_EN_PASSANT__ ;
	    $new_ml->add_move($piece, $sq1, $sq2, $flags);
	    if($p2_move) {
			$p2_piece = $old_to_new[spl_object_hash($p2_move->get_piece())];
			$p2_sq1 = $p2_move->get_start_square();
			$p2_sq2 = $p2_move->get_dest_square();
			$p2_flags = 0x0;
			if ($p2_move->is_capture()) $p2_flags |= self::__MOVE_CAPTURE__ ;
			if ($p2_move->is_short_castle()) $p2_flags |= self::__MOVE_CASTLE_SHORT__ ;
			if ($p2_move->is_long_castle()) $p2_flags |= self::__MOVE_CASTLE_LONG__ ;
			if ($p2_move->is_en_passant()) $p2_flags |= self::__MOVE_EN_PASSANT__ ;
			$new_ml->add_move($p2_piece, $p2_sq1, $p2_sq2, $p2_flags);
	    }
	}

	$new_obj->movelist = $new_ml;
	
	$this->movelist	= $new_obj->movelist;
	$this->player_has_moves	= $new_obj->player_has_moves;
	$this->captures	= $captures;
	$this->pieces	= $new_obj->pieces;
	$this->players	= $new_obj->players;
	$this->kings	= $new_obj->kings;
	$this->board	= $clone;

    }
       
    
    
    
  	/**
  	 * Creates one or several Piece objects and add them to the $squares 
  	 * positions on the board.
  	 * 
  	 * @param string $type classname of the piece
  	 * @param array $squares positions on the board from a1 to h8
  	 * @param string $player owner's name
  	 * @return string hash identifier of the piece
  	 */
    private function add_pieces($type, $squares, $player){
    	foreach($squares as $sq){
    		$fqn = ucfirst($type);
    		$piece = new $fqn($sq, $player);
    		$this->board->set_piece_at($sq, $piece);
    		$hash = spl_object_hash($piece);
    		$this->pieces[$player][$hash]	= $piece;	
    	} 
    	return $hash;
    }
    
    
    
    
    
    /**
     * Returns the board object of this game
     * 
     * @return Board game's board instance
     * 
     */
    
  	public function get_board(){
    	return $this->board;    
    }
    
    
    
    
    /**
     * Returns all the pieces of the given player's name or both players 
     * if $player is not specified
     * 
     * @param string $player player's name
     * @return array pieces of the player(s)
     * 
     */
    
  	public function get_pieces($player = ''){
    	if ($player != '' && array_key_exists($player, $this->pieces)){
			return $this->pieces[$player];
    	}
    	else{
			$player1 = $this->players[0];
			$player2 = $this->players[1];
			return array($this->pieces[$player1], $this->pieces[$player2]);
    	} 
    }
    
    
    
    /**
     * Returns an array containing the two players's names
     * 
     * @return array player's names
     * 
     */
    
  	public function get_players(){
    	return $this->players;    
    }
    
    
    
    
    /**
     * Returns the game's movelist object
     * 
     * @return MoveList game's movelist instance
     * 
     */
    
  	public function get_movelist(){
    	return $this->movelist;  
    }
    
    
    
    
    /**
     * Returns the message containing the reason "make_move()" 
     * or "is_move_legal()" returned false, such as "Can't castle out of check".
     * 
     * @return string message generated by the last move
     */
    
  	public function get_message(){
  		#get message and delete it
	    $msg = $this->message;
	    $this->message = '';
	    
	    return $msg;    
    }
    
    
    
    
    /**
     * Returns the captured piece on the given move number
     * 
     * @param string $player player's name
     * @param integer $movenum move number
     * 
     * @return Piece|bool returns a Piece instance or false if no capture were found
     */
    
  	public function get_capture($player, $movenum){
  		#get captures
	    $captures = $this->captures;

	    #Check player and movenum exist
	    if(array_key_exists($player, $captures) == false) return false;
	    if(array_key_exists($movenum, $captures[$player]) == false) return false;
	  
	    return $captures[$player][$movenum];    
    }
    
    
    
    
    /**
     * Looks for a threatened king from the given game's instance and set the correct flag
     * of it.  
     * 
     * @param Game $game a Game instance. Usually a cloned Game object.
     * 
     */
    
  	private function _mark_threatened_kings($game){
  	$player1 	= $game->players[0];
    	$player2 	= $game->players[1];
    	$p1_pieces 	= $game->pieces[$player1];
    	$p2_pieces 	= $game->pieces[$player2];
    	$movelist 	= $game->movelist;
    	$p1_king 	= $game->kings[0];
    	$p2_king 	= $game->kings[1];
    	$board 		= $game->board;
    	$p1_king->set_threatened(0);
    	$p2_king->set_threatened(0);
    	
    	foreach($p1_pieces as $p1_piece){
    	                if (get_class($p1_piece) == "Game") continue;
			if ($p1_piece->captured()) 
				continue;
			$p1_sq = $p1_piece->get_current_square();
			$p2_sq = $p2_king->get_current_square();
			if(!$p1_piece->can_reach($p2_sq))
				continue;
			if($p1_piece instanceof Pawn){
			    if (Board::horz_distance($p1_sq, $p2_sq) == 0)
			    	continue;
			}
			elseif($p1_piece instanceof King){
			    if(Board::horz_distance($p1_sq, $p2_sq) == 2)
			    	continue;
			}
			elseif(!$p1_piece instanceof Knight){
			    $board_c = clone $board;
			    $board_c->set_piece_at($p1_sq, null);
			    $board_c->set_piece_at($p2_sq, null);
			    if(!$board_c->line_is_open($p1_sq, $p2_sq))
			    	continue;
			}
 			$p2_king->set_threatened(true);
 			break; //No need for further checking
    	}
  	    
    	foreach($p2_pieces as $p2_piece){
    	                if (get_class($p2_piece) == "Game") continue;
			if ($p2_piece->captured())
				continue;
			$p2_sq = $p2_piece->get_current_square();
			$p1_sq = $p1_king->get_current_square();
			if(!$p2_piece->can_reach($p1_sq))
				continue;
			if($p2_piece instanceof Pawn){
			    if (Board::horz_distance($p1_sq, $p2_sq) == 0)
			    	continue;
			}
			elseif($p2_piece instanceof King){
			    if(Board::horz_distance($p1_sq, $p2_sq) == 2)
			    	continue;
			}
			elseif(!$p2_piece instanceof Knight){
			    $board_c = clone $board;
			    $board_c->set_piece_at($p1_sq, null);
			    $board_c->set_piece_at($p2_sq, null);
			    if(!$board_c->line_is_open($p1_sq, $p2_sq))
			    	continue;
			}
			$p1_king->set_threatened(true);
			break; //No need for further checking
    	}   
    }
    
    
    
    
    /**
     * Checks whether the given piece follow a en passant move
     * 
     * @param Piece $piece the piece to check
     * @param string $sq1 current position of the piece
     * @param string $sq2 destination of the piece
     * 
     * @return bool returns true if 'en passant' is valid or false otherwise
     */
        
  	private function _is_valid_en_passant($piece, $sq1, $sq2){
	    $movelist 	= $this->movelist;
	    $movenum  	= $movelist->get_move_num();
	    $last_moved = $movelist->get_last_moved();
	    $move 		= $movelist->get_move($movenum, $last_moved);
	    
	    if(!$move) return false;

	    $piece2 = $move->get_piece();
	    if(!$piece2 instanceof Pawn) return false;
	    
	    $player1 	= $this->players[0];
	    $player2 	= $this->players[1];
	    $p2_sq 		= $piece2->get_current_square();
	    
	    if ($piece2->get_player() == $player1) {
			if(Board::square_up_from($sq2) != $p2_sq) return false;
	    }
	    else{
	    	if(Board::square_down_from($sq2) != $p2_sq) return false;
	    }
	    
	    return true;    
    }
    
    
    
    
    
    /**
     * Checks whether the given piece follow a short castle move
     * 
     * @param Piece $piece the piece to check
     * @param string $sq1 current position of the piece
     * @param string $sq2 destination of the piece
     * 
     * @return bool returns true if 'short castle' is valid or false otherwise
     */    
    
  	private function _is_valid_short_castle($piece, $sq1, $sq2){
  		$player1 	= $this->players[0];
    	$player2 	= $this->players[1];
	    $player 	= $piece->get_player();
	    $board 		= $this->board;
	    $tsq 		= $player == $player1 ? "g1" : "g8";
	    if($sq2 != $tsq) return false; 
	    if($piece->moved()) {
			$this->message = ucfirst($player) . "'s king has already moved";
			return false;
	    }
	   
	    $rook = NULL;
	    if($player == $player1) {
			$rook = $board->get_piece_at("h1");
	    }
	    else{
			$rook = $board->get_piece_at("h8");
	    }
	    
	    if($rook == false || $rook->moved()) {
			$this->message = ucfirst($player) . "'s kingside rook has already moved";
			return false;
	    }
	    
	    $rook_sq = $player == $player1 ? "h1" : "h8";
	    $king_sq = $player == $player1 ? "e1" : "e8";
	    $board_c = clone $board;
	    $board_c->set_piece_at($king_sq, null);
	    $board_c->set_piece_at($rook_sq, null);
	    if($board_c->line_is_open($king_sq, $rook_sq) == false) {
			$this->message = "There are pieces between " . ucfirst($player) . "'s king and rook";
			return false;
	    }
	    
	    return true;    
    }
    
    
    
    
    
     /**
     * Checks whether the given piece follow a long castle move
     * 
     * @param Piece $piece the piece to check
     * @param string $sq1 current position of the piece
     * @param string $sq2 destination of the piece
     * 
     * @return bool returns true if 'long castle' is valid or false otherwise
     */      
  	private function _is_valid_long_castle($piece, $sq1, $sq2){
  	    $player1 	= $this->players[0];
            $player2 	= $this->players[1];
	    $player 	= $piece->get_player();
	    $board	= $this->board;
	    $tsq 	= $player == $player1 ? "c1" : "c8";
	    
	    if($sq2 != $tsq) return false;
	    
	    if($piece->moved()) {
			$this->message = ucfirst($player) . "'s king has already moved";
			return false;
	    }
	    
	    $rook == NULL;
	    if($player == $player1) {
			$rook = $board->get_piece_at("a1");
	    }
	    else {
			$rook = $board->get_piece_at("a8");
	    }
	    
	    if($rook == false || $rook->moved()) {
			$this->message = ucfirst($player) . "'s queenside rook has already moved";
			return false;
	    }
	    
	    $rook_sq = $player == $player1 ? "a1" : "a8";
	    $king_sq = $player == $player1 ? "e1" : "e8";
	    $board_c = clone $board;
	    $board_c->set_piece_at($king_sq, NULL);
	    $board_c->set_piece_at($rook_sq, NULL);
	    if($board_c->line_is_open($king_sq, $rook_sq) == false) {
			$this->message = "There are pieces between " . ucfirst($player) . "'s king and rook";
			return false;
	    }
	    
	    return true;    
    }
    
    
    
    
    /**
     * Takes two parameters containing the name of the square to move 
     * from and the name of the square to move to. They should be validated 
     * with "square_is_valid()" in Board prior to calling. Returns true 
     * if the provided move is legal within the context of the current game.
     * 
     * @param string $sq1 current position of the piece
     * @param string $sq2 destination of the piece
     * 
     * @return bool true if the move is legal or false otherwise
     */
  	public function is_move_legal($sq1, $sq2){
  		#check squares are on the board
	    if(Board::square_is_valid($sq1) == false) return false;
	    if(Board::square_is_valid($sq2) == false) return false;

  	    $player1 		= $this->players[0];
            $player2 		= $this->players[1];
	    $board 		= $this->board;
	    $piece 		= $board->get_piece_at($sq1);
	    
	    #trying to move invisible piece
	    if($piece == NULL) return false;
	    
	    $player 	= $piece->get_player();
	    $movelist 	= $this->movelist;
	    $last_moved = $movelist->get_last_moved();
	    
	    #check whose turn is
	    if(($last_moved != NULL && $last_moved == $player) ||
							($last_moved == NULL && $player != $player1)) {
			$this->message = "Not your turn";
			return false;
	    }
	    
	    #square not reacheable for this piece
	    if($piece->can_reach($sq2) == false) return false;
	   
	    
	    #if capturing anything
	    $capture = $board->get_piece_at($sq2);
	    if($capture) {
			if($capture->get_player() == $player) {
		    	$this->message = "You can't capture your own piece";
		    	return false;
			}
			#Pawn try to capture
			if ($piece instanceof Pawn) {
			    	if(abs(Board::horz_distance($sq1, $sq2)) != 1) {
					$this->message = "Pawns may only capture diagonally";
					return false;
			    }
			}
			#King try to capture
			elseif ($piece instanceof King) {
			    	if(abs(Board::horz_distance($sq1, $sq2)) >= 2) {
					$this->message = "You can't capture while castling";
					return false;
			    }
			}
	    }
	    else {
	    	#Check "en passant" capture
			if($piece instanceof Pawn) {
			    $ml = $piece->movelist;
			    if(Board::horz_distance($sq1, $sq2) != 0 &&
			            $this->_is_valid_en_passant($piece, $sq1, $sq2) == false) {
					$this->message = "Pawns must capture on a diagonal move";
					return false;
			    }
			}
	    }
	    
	    $valid_castle 	= 0;
	    $clone 			= clone $this;
	    $king 			= $clone->kings[($player == $player1 ? 0 : 1)];

	    #Piece is a King
	    if($piece instanceof King) {
			$hdist = Board::horz_distance($sq1, $sq2);
			if(abs($hdist) == 2) {
			    self::_mark_threatened_kings($clone);
			    if($king->threatened()) {
					$this->message = "Can't castle out of check";
					return false;
			    }
			    if($hdist > 0){
					if($this->_is_valid_short_castle($piece, $sq1, $sq2) == false) return false;
					$valid_castle = self::__MOVE_CASTLE_SHORT__;
			    }
			    else{
					if($this->_is_valid_long_castle($piece, $sq1, $sq2) == false) return false;
					$valid_castle = self::__MOVE_CASTLE_LONG__;
			    }
			}
	    }
	    #Piece is not a King
	    elseif(!$piece instanceof King) {
			$board_c = clone $board;
			$board_c->set_piece_at($sq1, NULL);
			$board_c->set_piece_at($sq2, NULL);
			if(!$piece instanceof Knight && $board_c->line_is_open($sq1, $sq2) == false) {
			    $this->message = "Line '$sq1' - '$sq2' is blocked";
			    return false;
			}
	    }
	    
	    #move is not a castle
	    if(!$valid_castle) {
			$clone->make_move($sq1, $sq2, false);
			self::_mark_threatened_kings($clone);
			if($king->threatened()) {
			    $this->message = "Move leaves your king in check";
			    return false;
			}
	    }
	    #Castle move is valid so far
	    else{
	    	#Short castle move
			if($valid_castle == self::__MOVE_CASTLE_SHORT__) {
			    $tsq = Board::square_right_of($sq1);
		    	$clone->make_move($sq1, $tsq, 0);
			    self::_mark_threatened_kings($clone);
			    if($king->threatened()) {
					$this->message = "Can't castle through check";
					return false;
			    }
			    $clone->make_move($tsq, $sq2, 0);
			    self::_mark_threatened_kings($clone);
			    if($king->threatened()) {
					$this->message = "Move leaves your king in check";
					return false;
			    }
			}
			#Long castle move
			else {			
			    $tsq = Board::square_left_of($sq1);
			    $clone->make_move($sq1, $tsq, 0);
			    self::_mark_threatened_kings($clone);
			    if($king->threatened()) {
					$this->message = "Can't castle through check";
					return false;
			    }
			    $clone->make_move($tsq, $sq2, 0);
			    self::_mark_threatened_kings($clone);
			    if($king->threatened()) {die();
					$this->message = "Move leaves your king in check";
					return false;
			    }
			}
	    }
	    $this->message = '';
	    return true;    
    }
    
    
    
    /**
      * Returns an array of all legal moves that can be made at the present time.
      * Can provide a pieceType and only moves of that piece type will be returned.
      */
    public function legal_moves($pieceType="") {
        $movelist 	= $this->movelist;
	$last_moved 	= $movelist->get_last_moved();
	if ($last_moved==NULL) $last_moved="Black";
	$player1 	= $this->players[0];
	$player2 	= $this->players[1];
	$player 	= $last_moved == $player1 ? $player2 : $player1;
	$pieces		= $this->pieces[$player];
        $legal = array();
	foreach ($pieces as $piece) {
	    if ($pieceType != "" && !($piece instanceOf $pieceType)) continue;
            $rsqs   = $piece->reachable_squares();
            $csq    = $piece->get_current_square();
            foreach ($rsqs as $sq) {
                if ($this->is_move_legal($csq,$sq)) array_push($legal, $csq . ',' . $sq);
            }
        }
        return $legal;
    }   
    
    /**
     * Returns and MD5 hash of the board, which should be unique based on the currently
     * possible moves and castleability.  First we get a list of all possible pawn moves
     * which will indicate en-passant possible moved too.  Then we generate a list of the
     * initial and current positions of every piece.  Then each King and Rook initial
     * position is checked to determine if the piece had moved prior.  Everything is
     * concatenated and then MD5 summed.  This can used for checking for 3rd repetition
     * draws.
     */
     
    public function boardMD5() {
        $possiblePawnMoves = serialize($this->legal_moves("Pawn"));
        $pieceList = '';
        for ($r = 1; $r < 9;$r++)
            for ($f = a; $f < i; $f++)
                if (is_object($this->board->get_piece_at($f . $r)))
                    $pieceList .= $this->board->get_piece_at($f . $r)->get_initial_square() . $f . $r;
        $castlePieces = array('a1','e1','h1','a8','e8','h8');
        foreach ($castlePieces as $p) 
            $castleState .= $this->board->get_piece_at($p)->moved();
        $result = md5($possiblePawnMoves . $pieceList . $castleState);
        return $result;
    }
        
    /**
     * Takes two parameters containing the name of the square to move from and 
     * the name of the square to move to. They should be validated with 
     * "square_is_valid()" in Board before calling. Optionally takes a 
     * third parameter, which can be set to zero to indicate that no legality 
     * checking should be done. In this case, flags indicating 'en passant' pawn 
     * captures or castling will not be set! Only by entirely validating the move 
     * do these flags have any meaning. The default is to validate every move. 
     * 
     * @param string $sq1 current position of the piece
     * @param string $sq2 destination of the piece
     * @param bool $validate whether to validate the move 
     * 
     * @return MoveListEntry|NULL a MoveListEntry representing the move just made or NULL
     * if any errors occur and the move was not considered  
     */
  	public function make_move($sq1, $sq2, $validate = true){
	    $move = NULL;

	    #Invalid square
	    if(Board::square_is_valid($sq1) == false) {
			return NULL;
	    }
	    if(Board::square_is_valid($sq2) == false) {
			return NULL;
	    }
	    if ($validate) {
			if($this->is_move_legal($sq1, $sq2) == false)
				return NULL;
	    }
	 
  		$player1 	= $this->players[0];
    	$player2 	= $this->players[1];
	    $board 		= $this->board;
	    $piece 		= $board->get_piece_at($sq1);
	    $player 	= $piece->get_player();

	    #if no piece at sq1
	    if($piece == NULL) {
			return NULL;
	    }
	  
	    $movelist 	= $this->movelist;
	    $capture 	= $board->get_piece_at($sq2);
	    $flags 		= 0x0;
	    
	    if ($validate && $piece instanceof Pawn) {
			if ($player == $player1) {
			    if(Board::vert_distance("d8", $sq2) == 0)
			    	$flags = $flags | self::__MOVE_PROMOTE__ ; //Bitwise Or
			}
			else {
			    if(Board::vert_distance("d1", $sq2) == 0)
			    	$flags = $flags | self::__MOVE_PROMOTE__; //Bitwise Or
			}
	    }
	    
	    if ($capture) {
			$flags = $flags | self::__MOVE_CAPTURE__;  //Bitwise Or
			$capture->set_captured(1);
			$board->set_piece_at($sq1, NULL);
			$board->set_piece_at($sq2, $piece);
			$piece->set_current_square($sq2);
			$piece->set_moved(1);
			$move 	 = $movelist->add_move($piece, $sq1, $sq2, $flags);
			$movenum = $move->get_move_num();
			$this->captures[$player][$movenum] = $capture;
	    }
	    else {
			if ($validate && $piece instanceof Pawn && $this->_is_valid_en_passant($piece, $sq1, $sq2)) {
			    $last_moved = $movelist->get_last_moved();
			    $move 		= $movelist->get_move($movelist->get_move_num(), $last_moved);
			    $capture	= $move->get_piece();
			    $flags 		= $flags | self::__MOVE_CAPTURE__;
			    $flags 		= $flags | self::__MOVE_EN_PASSANT__;
			    $capture->set_captured(1);
			    $board->set_piece_at($sq1, NULL);
			    $board->set_piece_at($sq2, $piece);
			    $piece->set_current_square($sq2);
			    $move = $movelist->add_move($piece, $sq1, $sq2, $flags);
			    $this->analyzed = 0;
			    $movenum = $move->get_move_num();
			    $piece->_set_firstmoved($movenum);
			    $this->captures[$player][$movenum] = $capture;
			}
			else {
			    if($validate && $piece instanceof King) {
					if($this->_is_valid_short_castle($piece, $sq1, $sq2))
						$flags = $flags | self::__MOVE_CASTLE_SHORT__ ;
					if($this->_is_valid_long_castle($piece, $sq1, $sq2)) 
						$flags = $flags | self::__MOVE_CASTLE_LONG__ ;
			    }
			    
			    if (($flags & self::__MOVE_CASTLE_SHORT__) || ($flags & self::__MOVE_CASTLE_LONG__)) {
					$rook_sq = $king_sq = $rook_sq_new = $king_sq_new = NULL;
					$rook = $king = NULL;
					
					if($player == $player1) {
					    $rook_sq 		= $flags & self::__MOVE_CASTLE_SHORT__ ? "h1" : "a1";
					    $rook_sq_new 	= $flags & self::__MOVE_CASTLE_SHORT__ ? "f1" : "d1";
					    $king_sq 		= "e1";
					    $king_sq_new 	= $flags & self::__MOVE_CASTLE_SHORT__ ? "g1" : "c1";
					}
					else{
					    $rook_sq 		= $flags & self::__MOVE_CASTLE_SHORT__ ? "h8" : "a8";
					    $rook_sq_new 	= $flags & self::__MOVE_CASTLE_SHORT__ ? "f8" : "d8";
					    $king_sq 		= "e8";
					    $king_sq_new 	= $flags & self::__MOVE_CASTLE_SHORT__ ? "g8" : "c8";
					}
					
					$king = $board->get_piece_at($king_sq);
					$rook = $board->get_piece_at($rook_sq);
					$board->set_piece_at($king_sq, NULL);
					$board->set_piece_at($king_sq_new, $king);
					$king->set_current_square($king_sq_new);
					$king->set_moved(1);
					$board->set_piece_at($rook_sq, NULL);
					$board->set_piece_at($rook_sq_new, $rook);
					$rook->set_current_square($rook_sq_new);
					$rook->set_moved(1);
				    $move = $movelist->add_move($piece, $sq1, $sq2, $flags);
					$movenum = $move->get_move_num();
					$this->analyzed = 0;
					$king->_set_firstmoved($movenum);
					$rook->_set_firstmoved($movenum);
			    }
			    else { 
					$board->set_piece_at($sq1, NULL);
					$board->set_piece_at($sq2, $piece);
					$piece->set_current_square($sq2);
					$piece->set_moved(1);
					$move = $movelist->add_move($piece, $sq1, $sq2, $flags);
					$movenum = $move->get_move_num();
					$piece->_set_firstmoved($movenum);
			    }
			}
	    }
	    return $move;    
    }
    
    
    
    
    /**
     * Returns MoveListEntry representing the 
     * last move made, and sets the state of the game to what it was prior to the 
     * returned move being made.
     * 
     * @return MoveListEntry the last MoveListEntry object of the move that has been taken back
     */
  	public function take_back_move(){
	    $movelist 		= $this->movelist;
	    $board 			= $this->board;
	    $curr_player 	= $movelist->get_last_moved();
	    $player1 		= $this->players[0];
	    $move 			= $movelist->delete_move();
	    
	    #There is a existing last move 
	    if($move) {
			$movenum 	= $move->get_move_num();
			$piece 		= $move->get_piece();
			$player 	= $piece->get_player();
			$ssq 		= $move->get_start_square();
			$dsq 		= $move->get_dest_square();
			
			#If last move was a promotion
			if($move->is_promotion()) {
			    //delete the prometed piece
			    $hash = $move->get_promoted_hash();
			    unset($this->pieces[$piece->get_player()][$hash]);
			}
			
			#If last move was a capture
			if($move->is_capture()) {
			    $capture = $this->captures[$player][$movenum];
			    if($move->is_en_passant()){
					if($player == $player1) {
					    $dsq = Board::square_down_from($dsq);
					}
					else {
					    $dsq = Board::square_up_from($dsq);
					}
			    }
			    
			    $board->set_piece_at($dsq, $capture);
			    $capture->set_current_square($dsq);
			    $capture->set_captured(0); 
			    $board->set_piece_at($ssq, $piece);
			    $piece->set_current_square($ssq);
			    if($piece->_firstmoved() == $movenum)
			    	$piece->set_moved(0);
			}
			#If last move was a short castle
			elseif ($move->is_short_castle()) {
			    $king_sq 		= $player == $player1 ? "e1" : "e8";
			    $rook_sq 		= $player == $player1 ? "h1" : "h8";
			    $king_curr_sq	= $player == $player1 ? "g1" : "g8";
			    $rook_curr_sq 	= $player == $player1 ? "f1" : "f8";
			    $rook 			= $board->get_piece_at($rook_curr_sq);
			    $board->set_piece_at($king_curr_sq, NULL);
			    $board->set_piece_at($rook_curr_sq, NULL);
			    $board->set_piece_at($king_sq, $piece);
			    $board->set_piece_at($rook_sq, $rook);
			    $rook->set_current_square($rook_sq);
			    $piece->set_current_square($king_sq);
			    $rook->set_moved(0);
			    $piece->set_moved(0);
			}
			#If last move was a long castle
			elseif ($move->is_long_castle()) {
			    $king_sq 		= $player == $player1 ? "e1" : "e8";
			    $rook_sq 		= $player == $player1 ? "a1" : "a8";
			    $king_curr_sq 	= $player == $player1 ? "c1" : "c8";
			    $rook_curr_sq 	= $player == $player1 ? "d1" : "d8";
			    $rook 			= $board->get_piece_at($rook_curr_sq);
			    $board->set_piece_at($king_curr_sq, NULL);
			    $board->set_piece_at($rook_curr_sq, NULL);
			    $board->set_piece_at($king_sq, $piece);
			    $board->set_piece_at($rook_sq, $rook);
			    $rook->set_current_square($rook_sq);
			    $piece->set_current_square($king_sq);
			    $rook->set_moved(0);
			    $piece->set_moved(0);
			}
			#otherwise last move was just a move
			else {
			    $board->set_piece_at($dsq, NULL);
			    $board->set_piece_at($ssq, $piece);
			    $piece->set_current_square($ssq);
			    if($piece->_firstmoved() == $movenum)
			    	$piece->set_moved(0) ;
			}
			
			#delete last
			unset($this->player_has_moves[$player][$movenum]);
	    }
	    
	    return $move;    
    }
    
    
    
    
    /** 
     * Checks whether the given player can move any pieces on the board from the current
     * game position and returns the number of possible moves.
     * 
     * @param string $player player's name
     * 
     * @return bool|integer the number of possible moves or false if player can't move any pieces
     */
    
  	private function _player_has_moves($player){
	    $movelist 	= $this->movelist;
	    $movenum 	= $movelist->get_move_num();
	    
	    if (array_key_exists($movenum, $this->player_has_moves[$player])) {
			return $this->player_has_moves[$player][$movenum];
	    }
	    
	    foreach($this->pieces[$player] as $piece){
			if(!$piece instanceof King && $piece->captured())
				continue;
			$rsqs 	= $piece->reachable_squares();
			$csq 	= $piece->get_current_square();
			foreach($rsqs as $sq){
			    if ($this->is_move_legal($csq, $sq)){
						$this->player_has_moves[$player][$movenum] = 1;
						return true;
			    }
			}
	    }
	   
	   	$this->player_has_moves[$player][$movenum] = 0;
	    return false;    
    }
    
    
    
    
    /**
     * Takes one parameters. If the last move was a promotion (as determined by a 
     * call to "is_promotion()" in MoveListEntry, then calling this 
     * function will change the newly promoted pawn to the piece specified by the 
     * provided parameter. Valid values are (case-insensitive) "bishop", "knight", "queen" and "rook".
     * 
     * @param string $new_piece classname of the piece
     * 
     * @return bool returns false if promotion is not valid
     */
  	public function do_promotion($new_piece){
	    $board 		= $this->board;
	    $movelist 	= $this->movelist;
	    $movenum 	= $movelist->get_move_num();
	    $last_moved = $movelist->get_last_moved();
	    $move 		= $movelist->get_move($movenum, $last_moved);
	    
	    #If move is not a promotion
	    if($move->is_promotion() == false)
	    	return false;
	    	
	    #do the promotion
	    $piece 		= $move->get_piece();
	    $csq 		= $piece->get_current_square();
	    $promoted 	= $piece->promote($new_piece);
	    $board->set_piece_at($csq, $promoted);
	    $this->pieces[$piece->get_player()][spl_object_hash($promoted)]	= $promoted;
	    $move->set_promoted_to($new_piece);  
	    $move->set_promoted_hash(spl_object_hash($promoted));   
	    unset($this->pieces[$piece->get_player()][spl_object_hash($piece)]);
    }
    
    
    
    
    /**
     * Takes a single parameter containing the name of the player to consider.
     * Returns true if the named player is in check.
     * 
     * @param string $player player's name
     *  
     * @return bool true if the player is in check or false otherwise
     */
  	public function player_in_check($player){
	    $player1 = $this->players[0];
	    self::_mark_threatened_kings($this);
	    $king = $this->kings[$player == $player1 ? 0 : 1];
	    return (bool)$king->threatened();    
    }
    
    
    
    
    /**
     * Takes a single parameter containing the name of the player to consider.
     * Returns true if the named player has been checkmated.
     * 
     * @param string $player player's name
     * 
     * @return bool true if the player is checkmated or false otherwise
     */
  	public function player_checkmated($player){
  	
  		#If player not in check
	    if($this->player_in_check($player) == false)
	    	return false ;
	    
	    #Player can move?
	    if($this->_player_has_moves($player)){
			return false;
	    }

	    #Player cannot move
		return true;
    }
    
    
    

    /**
     * Takes a single parameter containing the name of the player to consider. 
     * Returns true if the named player has been stalemated.
     * 
     * @param string $player player's name
     * 
     * @return bool true if the player is stalemated or false otherwise
     */
  	public function player_stalemated($player){
  	
  		#If player in check
  	   	if($this->player_in_check($player) == true)
  	   		return false;
  	   		
  	   	#Player can move?
	    if($this->_player_has_moves($player)) {
			return false;
	    }

	    #Player cannot move
	    return true;
    }
    
    
    
    
    /**
     * Result of the game.
     * 
     * Returns false as long as the game is in progress. 
     * When a conclusion has been reached, returns true if the first player checkmated 
     * the second player.
     * NULL if either player has been stalemated.
     * 1 if the second player checkmated the first player.
     * 
     * Is not currently able to determine if the game was drawn by a three-fold
     * repetition of positions.
     * 
     * @return bool|integer
     */
  	public function result(){
	    $movelist 	= $this->movelist;
	    $last_moved = $movelist->get_last_moved();
	    $player1 	= $this->players[0];
	    $player2 	= $this->players[1];
	    $player 	= $last_moved == $player1 ? $player2 : $player1;
	    
	    if($this->_player_has_moves($player))
	    	return false;
	    if($this->player_stalemated($player)) 
	    	return NULL;
	    if ($this->player_checkmated($player) && $player == $player2) 
	    	return true;
	   	if ($this->player_checkmated($player)) 
	   		return 1;    
    }
    

  }



