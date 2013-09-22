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

  
  abstract class Piece{  
  
    const __PIECE_MOVED__		= 0x01;
 	const __PIECE_THREATENED__	= 0x02;
  	const __PIECE_CAPTURED__	= 0x04;
  
    public $firstmoved 		= NULL;
    public $init_sq 		= '';
    public $curr_sq 		= '';
    public $player 			= '';
    public $description 	= '';
    public $flags 			= 0x0;
    
    
    
    
    /**
     * Constructs a new Piece. Requires a two scalar arguments containing the
     * initial square this piece is on and the color of the piece. If the program
     * will use colors other than 'black' and 'white', then subclasses of
     * Piece will need to override the method to take these
     * colors into account.  Optionally takes a third argument containing a text 
     * description of the piece. Returns a Piece object reference. 
     * The square is not tested for validity, so the program must validate the 
     * square before calling new Piece().
     * 
     * @param string $init_sq initial square position
     * @param string $color color of the piece
     * @param string $desc description of the piece
     * @return Piece
     * 
     */
    function __construct($init_sq, $color, $desc = ''){
	    $this->init_sq 		= $init_sq;
		$this->curr_sq 		= $init_sq;
		$this->player 		= strtolower($color);
		if($desc != '')
			$this->description 	= $desc;

		return $this;
  	}

  	
  	
  	
  	/**
  	 * Returns whether the piece has been moved yet by returning the move number or NULL
  	 * 
  	 * @return integer|NULL
  	 */
  	public function _firstmoved(){
		return $this->firstmoved;  	
  	}
  	
  	
  	
  	
  	/**
  	 * Sets the first move number of the piece
  	 * 
  	 * @param integer $movenum move number
  	 */
  	public function _set_firstmoved($movenum){
		$this->firstmoved = $movenum;  	
  	}
  	
  	
  	
  	
  	/**
  	 * Returns the initial square property that the piece was constructed with.
  	 * 
  	 * @return string initial square position
  	 */
  	public function get_initial_square(){
  		return $this->init_sq;
  	}
  	
  	
  	
  	
  	/**
  	 * Returns the value of the current square property.
  	 * 
  	 *  @return string current square position
  	 */
  	public function get_current_square(){
  		return $this->curr_sq;
  	}
  	
  	
  	
  	
  	/**
  	 * Takes a single scalar parameter containing the current square of this piece.
  	 * Sets the current square property to this value. Like __construct, this square
  	 * is not tested for validity and should be tested before calling the function.
  	 * 
  	 * @param string $sq set the piece at this square position
  	 */
  	public function set_current_square($sq){
  		 $this->curr_sq = $sq;
  	}
  	
  	
  	
  	
  	/**
  	 * Returns the value of the description property.
  	 * 
  	 * @return string description of the piece
  	 */
  	public function get_description(){
  		return $this->description;
  	}
  	
  	
  	
  	
  	/**
  	 * Takes a single scalar parameter containing a description for the piece.
  	 * Sets the description property to this value.
  	 * 
  	 * @param string $desc description of the piece
  	 */
    public function set_description($desc){
    	$this->description = $desc;
    }
    
    
    
    
    /**
     * Returns the piece owner's name
     * 
     * @return string piece owner's name
     */
    public function get_player(){
    	return $this->player;
    }
    
    
    
    
    /**
     * Returns if the piece has not been moved as determined by a call to set_moved().
     * 
     * @return integer
     */
    public function moved(){
    	return $this->flags & self::__PIECE_MOVED__;
    }
    
    
    
    
    /**
     * Takes a single scalar parameter containing true or false. Sets the moved flag
     * if the parameter is true.
     * 
     * @param bool $set set the piece as moved or not
     */
    public function set_moved($set){
	    if($set)
	    	$this->flags = $this->flags | self::__PIECE_MOVED__ ; 
	    	//0x0->0x1  0x01->0x01   0x02->0x03  0x03->0x03  0x04->0x05  0x05->0x05
	    else
	    	$this->flags = $this->flags & ~self::__PIECE_MOVED__ ; 
	    	//0x0->0x0  0x01->0x0   0x02->0x02   0x03->0x02   0x04->0x04  0x05->0x04
    }
    
    
    
    
    /**
     * Returns true if the piece is not threatened as determined by a call to et_threatened().
     * 
     * @return integer
     */
    public function threatened(){
    	return $this->flags & self::__PIECE_THREATENED__;
    }
    
    
    
    
    /**
     * Takes a single scalar parameter containing true or false. Sets the threatened
     * flag if the parameter is true.
     * 
     * @param bool $set set the piece as threatened or not
     */
    public function set_threatened($set){
    	if ($set)
    		$this->flags = $this->flags |  self::__PIECE_THREATENED__ ; //add threatened bit to flags
    	else
    		$this->flags = $this->flags & ~self::__PIECE_THREATENED__ ; //remove threatened bit to flags  
    }
    
    
    
    
    /**
     * Returns true if the piece is not captured as determined by a call to set_captured()
     * 
     * @return integer
     */
    public function captured(){
    	return $this->flags & self::__PIECE_CAPTURED__;
    }
    
    
    
    
    /**
     * Takes a single scalar parameter containing true or false. Sets the captured
     * flag, and also sets the current square property to NULL, if the parameter is true.
     * 
     * @param bool $set set the piece as captured or not
     */
    public function set_captured($set){
	    if ($set) {
			$this->curr_sq = NULL;
			$this->flags = $this->flags | self::__PIECE_CAPTURED__; //add captured bit to flags
	    }
	    else {
			$this->flags = $this->flags & ~self::__PIECE_CAPTURED__; //remove captured bit to flags
	    }    
    }
    
    
    
    
    /**
     * Takes a single scalar parameter containing the square to be tested. Returns
     * true if the piece can reach the given square from its current location, as
     * determined by a call to the abstract method reachable_squares()
     * 
     * @param string $sq square position
     * @return bool true if the piece can reach the square or false otherwise
     */
    public function can_reach($sq){
    	return count(preg_grep("/^$sq$/", $this->reachable_squares())) > 0;
    }
    
    
    
    
    /**
     * This is an abstract method and must be overridden in all subclasses of
     * Piece. Returns a list of squares (in lower-case) that the piece can
     * reach. This list is used by can_reach() and various methods of
     * class Game to determine legality of moves and other high-level analyses.
     * Thus, subclasses of Piece not provided by this framework must return
     * all squares that may be reached, regardless of the current state of the
     * board. The Game::is_move_legal() method will then determine if all
     * conditions for a particular move have been met.
     * 
     * @return array reachable squares
     */
    abstract function reachable_squares();
    
    
    
    
    /**
     * Returns the color property the piece was constructed with.
     * 
     * @return string owner player's name of the piece
     */
    public function get_color(){
    	return $this->player;
    }
    
    
    
    
  }