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

  include_once('MoveListEntry.class.php');

  class MoveList{
  
  	protected $move_num		= 0;
    protected $players		= array();
    protected $last_moved	= NULL;
    protected $movelist		= array();
    
    
    
    /**
     * Creates a new MoveList. Takes two scalar parameters containing the
     * names of the two players. These names will be used as a key for calls to
     * get_move() and delete_move().
     * 
     * @param string $player1 player1's name
     * @param string $player2 player2's name
     * 
     * @return MoveList
     */
    function __construct($player1, $player2){
		$this->players[0]	= $player1;
		$this->players[1]	= $player2;
		$this->movelist[$player1] 	= array();
		$this->movelist[$player2] 	= array();     
     }
     
     
     
     
     /**
      * Clones the MoveList object and their MoveListEntry instances as well. Thus, 
      * allowing to work on a identical MoveList
      * 
      * @return MoveList
      */
     
     function __clone(){
     	#clone the movelistentry
     	$clonemvl	= array();
     	$clonemvl[$this->players[0]] = array();
     	$clonemvl[$this->players[1]] = array();
     	
     	foreach($this->movelist[$this->players[0]] as $move){
     		$clonemvl[$this->players[0]][] = clone $move;
     	}
     	
        foreach($this->movelist[$this->players[1]] as $move){
     		$clonemvl[$this->players[1]][] = clone $move;	
     	}   

     	$this->movelist[$this->players[0]] = $clonemvl[$this->players[0]];
     	$this->movelist[$this->players[1]] = $clonemvl[$this->players[1]];
     }
     
  
     
     
     
    /**
     * Returns the current move number of the game. Numbering is identical 
     * to numbering in a regular chess game. The move number does not
     * increment until the first player's next turn.
     * 
     * @return integer current move number of the game
     */
  	public function get_move_num(){
	    return $this->move_num + 1;  	
  	}
  	
  	
  	
  	
  	/**
  	 * Returns the name of the player who last moved. It will
  	 * be one of the values passed to new and can be used as a key to
  	 * get_move() and delete_move().
  	 * 
  	 * @return string last moved player's name
  	 */
  	public function get_last_moved(){
	    $last_moved = $this->last_moved;
	    if(!isset($last_moved))
	    	return false;
	    return $this->players[$last_moved];  	
  	}
  	
  	
  	
  	
  	/**
  	 * Returns an array containing the name of the players
  	 * 
  	 * @return array name of the players
  	 */
  	
  	public function get_players(){
  		return $this->players;
  	}
  	
  	
  	
  	
  	/**
  	 * Takes two scalar parameters containing the move number and the name of the
  	 * player to get the move for. Returns a MoveListEntry object
  	 * with the particulars for that move, or false if that move wasn't found.
  	 * 
  	 * @param integer $move_num move number
  	 * @param string $player player's name
  	 * @return MoveListEntry|bool MoveListEntry object or false otherwise
  	 */
  	public function get_move($move_num, $player){
  		if($player === false) return false;
  		
		if(array_key_exists($player, $this->movelist) == false)
	   		return false;
	   		
		if(array_key_exists($move_num - 1, $this->movelist[$player]) == false)
			return false;
			
	   return $this->movelist[$player][$move_num - 1];  	
  	}
  	
  	
  	
  	
  	/**
  	 * Takes an optional scalar parameter specifying which player to return a list
  	 * of moves for. Returns an array of all the entries for moves made by that
  	 * player. If the player is not specified, returns a two-element array containing
  	 * references to the first player's and second player's lists respectively.
  	 * 
  	 * @param string $player player's name
  	 * @return array|bool player(s)'s moves or false otherwise
  	 */
  	public function get_all_moves($player = ''){
  	
  		#no player specified
  		if($player == '')
  			return array($this->movelist[$this->players[0]], $this->movelist[$this->players[1]]);	
  			
  		#player name does not exist
  	    if(array_key_exists($player, $this->movelist) == false)
  	    	return false;
  	    	
		#player exists
		return $this->movelist[$player];	
  	}
  	
  	
  	
  	
  	/**
  	 * Takes three scalar parameters containing a reference to the piece being moved,
  	 * the square it is being moved from, and square it is being moved to. Returns
  	 * a MoveListEntry object containing the particulars for that move.
  	 * 
  	 * @param Piece $piece piece to move
  	 * @param string $sq1 square position being moved from
  	 * @param string $sq2 square position being moved to
  	 * @param integer $flags flag to set. See class constants.
  	 * @return MoveListEntry
  	 */
  	public function add_move($piece, $sq1, $sq2, $flags = false){
	    $move_num 	= $this->move_num;
	    $last_moved = $this->last_moved;
	    $turn = (isset($last_moved) && ($last_moved == 0)) ? 1 : 0;
	    if(isset($last_moved)) {
			if($turn == 0)
				$move_num++;
	    }
	    else {
			$move_num = 0;
	    }
	    $player = $this->players[$turn];
	    $entry 	= new MoveListEntry($move_num + 1, $piece, $sq1, $sq2, $flags);
	    $this->movelist[$player][$move_num] = $entry;
	    $this->last_moved 	= $turn;
	    $this->move_num 	= $move_num;
	    
	    return $entry;  	
  	}
  	
  	
  	
  	
  	/**
  	 * Returns the last move to be made, if there is one, and then deletes it.
  	 * The MoveList is now in exactly the same state as prior to the last move
  	 * being made. Returns false if there is no last move.
  	 * 
  	 * @return MoveListEntry|bool
  	 */
  	public function delete_move(){
	    $last_moved = $this->last_moved;
	    
	    #no last move yet
	    if(isset($last_moved) == false)
	    	return false;

	   	#Whose turn was it ?
	    $curr_move	= $this->move_num;
	    $player 	= $this->players[$last_moved];
	    $entry		= $this->movelist[$player][$curr_move];
	    
	    #delete the last move
	    unset($this->movelist[$player][$curr_move]);
	    
	    #Other player's turn now
	    $this->last_moved = $last_moved ? 0 : 1;
	    
	    #Player1's turn
	    if ($last_moved == 0) {
	    	#If we are at the begining of the game now
			if ($curr_move == 0) {
		    	    $this->last_moved = NULL;
			}
			else {
			    $this->move_num--;
			}
	    }

	    return $entry;  	
  	}
  	
  	
  	
  }