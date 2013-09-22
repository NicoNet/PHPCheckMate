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

  
  class Board{
    	
  	private static $emptyboard_arr 	= array();
  	private $board_arr 				= array();
  
  
  	/**
  	 * Takes no arguments. Returns a Board object reference. 
  	 * 
  	 */
  	function __construct(){
  		#Create the static empty board
  		if(empty(self::$emptyboard_arr))
  			self::$emptyboard_arr = $this->_get_empty_board();
  			
		#Create the board;  			
  		$this->board_arr = $this->_get_empty_board();
  	}
  	
    private function _get_empty_board() {
    $board_arr	= array();
    
	#color the board
	for($y = 0; $y < 8; $y++) {
	    $color = $y % 2 ? 'light' : 'dark';
	    for($x = 0; $x < 8;$x += 2) {
			$board_arr[$y][$x] = array('color' => $color, 'piece' => NULL);
			$color = $color == 'light' ? 'dark' : 'light';
			
			$board_arr[$y][$x+1] = array('color' => $color, 'piece' => NULL);
			$color = $color == 'light' ? 'dark' : 'light';
	    }
	}

	return $board_arr;
    }
  	
    public function get_board_arr() {
        return (object)$this->board_arr;
    }

  	
  	/**
  	 * Returns a Board object reference which isidentical to the caller object.
  	 * However, it is a copy which allows the clone()'d object to be manipulated
  	 * separately of the caller object.
  	 */
  	function __clone(){
  		#clone the pieces
  		for($y = 0; $y < 8; $y++) {
	    	for($x = 0; $x < 8;$x++) {
	    		if($this->board_arr[$y][$x]['piece'] != false)
					$this->board_arr[$y][$x]['piece'] 	= clone $this->board_arr[$y][$x]['piece'];				
	   	 	}
  		}
  	}
  	
  	

  	
  	
  	/**
  	 * Gets the coordinates of a square position
  	 * 
  	 * @param string $sq square position on the board
  	 * @return array x,y coordinates
  	 */
  	static function _get_square_coords($sq){
  		#If $sq is not a valid square
	    if (Board::square_is_valid($sq) == false) {
			return NULL;
	    }
	    $x = ord(strtolower(substr($sq, 0, 1))) - ord('a');
	    $y = substr($sq, 1, 1) - 1;
	    return array($x, $y);  	
  	}
  	
  	
  	
  	
  	/**
  	 * Converts coordinates into a square position
  	 * 
  	 * @param string $x x coordinate
  	 * @param string $y y coordinate
  	 * @return string square position on the board
  	 */
  	static function _coords_to_square($x, $y){
	    $sq = chr(ord('a') + $x) . ($y + 1);
	    
	    return $sq;  	
  	}
  	
  	
  	
  	
  	/**
  	 * Takes a single scalar parameter with the square to be tested. Returns true if
  	 * the given square falls within the range a1-h8. Returns false otherwise.
  	 * It is case-insensitive, though all functions that return squares will return
  	 * lower-case.
  	 * 
  	 * @param string $sq square position on the board
  	 * @return bool true if valid board position or false otherwise
  	 */
  	public static function square_is_valid($sq){
	    return preg_match('/^[A-Ha-h][1-8]$/',$sq) > 0;
  	}
  	
  	
  	
  	
  	/**
  	 * Takes a single scalar parameter containing the square whose color is requested.
  	 * Returns a scalar containing either of the strings 'light' or 'dark'. Returns
  	 * false if the square is not valid.
  	 * 
  	 * @param string $sq square position on the board
  	 * @return string|bool color of the square or false if square not valid
  	 */
  	public static function get_color_of($sq){
	    $r_board_arr = self::_get_empty_board();
	    list($x, $y) = self::_get_square_coords($sq);
	    if($x !== NULL && $y !== NULL){
			return $r_board_arr[$y][$x]['color'];
	    }
	    else {
			return false;
	    }  	
  	}
  	
  	
  	
  	
  	/**
  	 * Returns the square at the given distance from the horizontal 
  	 * 
  	 * @param string $sq square position on the board
  	 * @param integer $dist distance from the horizontal
  	 * @return string|bool square position found or false if falls out of the board
  	 */
  	public static function add_horz_distance($sq, $dist){
	    list($x, $y) = self::_get_square_coords($sq);
	    if($x === NULL || $y === NULL)
	    	return false;
	    $x += $dist;
	    if(($x < 0) || ($x > 7))
	    	return false;
	    $sq = self::_coords_to_square($x, $y);
	    
	    return $sq;  	
  	}
  	
  	
  	
  	/**
  	 * Returns the square at the given distance from the vertical 
  	 * 
  	 * @param string $sq square position on the board
  	 * @param integer $dist distance from the vertical
  	 * @return string|bool square position found or false if falls out of the board
  	 */  	
  	public static function add_vert_distance($sq, $dist){
	    list($x, $y) = self::_get_square_coords($sq);
	    if($x === NULL || $y === NULL)
	    	return false;
	    $y += $dist;
	    if(($y < 0) || ($y > 7))
	    	return false;
	    $sq = self::_coords_to_square($x, $y);
	    
	    return $sq;  	
  	}
  	
  	
  	
  	
  	/**
  	 * Takes a single scalar parameter containing the square to calculate distance
  	 * from. Returns the horizontal distance in squares between the two points.
  	 * 
  	 * @param string $sq1 square position 1
  	 * @param string $sq2 square position 2
  	 * @return integer distance in square that separates the two positions
  	 */
  	public static function horz_distance($sq1, $sq2){
	   list($x1, $y1) = self::_get_square_coords($sq1);
	   list($x2, $y2) = self::_get_square_coords($sq2);
	   
	   return $x2 - $x1;  	
  	}
  	
  	
  	
  	
  	/**
  	 * Takes a single scalar parameter containing the square to calculate distance
  	 * from. Returns the vertical distance in squares between the two points.
  	 * 
  	 * @param string $sq1 square position 1
  	 * @param string $sq2 square position 2
  	 * @return integer distance in square that separates the two positions
  	 */
  	public static function vert_distance($sq1, $sq2){
	    list($x1, $y1) = self::_get_square_coords($sq1);
	    list($x2, $y2) = self::_get_square_coords($sq2);
	    
	    return $y2 - $y1;  	
  	}
  	
  	
  	
  	
  	/**
  	 * Takes a single scalar parameter containing the square right of the requested
  	 * square. Returns a string containing the square left of the parameter. Returns
  	 * false otherwise.
  	 * 
  	 * @param string $sq square position
  	 * @return string|bool square right on the left or false otherwise
  	 */
  	public static function square_left_of($sq){
    	return self::add_horz_distance($sq, -1);  	
  	}
  	
  	
  	
  	
  	/**
  	 * Takes a single scalar parameter containing the square left of the requested
  	 * square. Returns a string containing the square right of the parameter. Returns
  	 * false otherwise.
  	 * 
  	 * @param string $sq square position
  	 * @return string|bool square right on the right or false otherwise
  	 */
  	public static function square_right_of($sq){
  		return self::add_horz_distance($sq, 1);
  	}
  	
  	
  	
  	
  	/**
  	 * Takes a single scalar parameter containing the square up from the requested
  	 * square. Returns a string containing the square down from the parameter. Returns
  	 * false otherwise.
  	 * 
  	 * @param string $sq square position
  	 * @return string|bool square right down or false otherwise
  	 */
  	public static function square_down_from($sq){
  		return self::add_vert_distance($sq, -1);
  	}
  	
  	
  	
  	
  	/**
  	 * Takes a single scalar parameter containing the square down from the requested
  	 * square. Returns a string containing the square up from the parameter. Returns
  	 * false otherwise.
  	 * 
  	 * @param string $sq square position
  	 * @return string|bool square right up or false otherwise
  	 */
  	public static function square_up_from($sq){
  		return self::add_vert_distance($sq, 1);
  	}
  	
  	
  	
  	
  	/**
  	 * Takes two scalar parameters containing two distinct endpoints in a line.
  	 * Returns a list of scalars in lower-case with an entry for each square in that
  	 * line, or false if the two endpoints do not define a line. In the case where
  	 * both squares are the same, will return a list containing that square.
  	 * 
  	 * @param string $sq1 square position 1
  	 * @param string $sq2 square position 2
  	 * @return array|bool squares following that line or false otherwise
  	 */
  	public static function squares_in_line($sq1, $sq2){
  		$squares = array();
  		
	    list($x1, $y1) = self::_get_square_coords($sq1);
	    list($x2, $y2) = self::_get_square_coords($sq2);
	    $hdist = abs($x2 - $x1);
	    $vdist = abs($y2 - $y1);
	    
	    if($hdist != 0 && $vdist != 0 && $hdist != $vdist)
	    	return false;
	    	
	    if($hdist == 0 && $vdist == 0)
	    	return array($sq1);
	    	
	    $hdelta = $hdist ? $hdist / ($x2 - $x1) : 0;
	  	$vdelta = $vdist ? $vdist / ($y2 - $y1) : 0;
	    
	    $sq = $sq1;
	    $squares[] = $sq;
	    if($vdist > 0 && $hdelta == 0) {
			for ($i = 0; $i < $vdist; $i++) {
			    $sq = $vdelta > 0 ? self::square_up_from($sq) : self::square_down_from($sq);
			    $squares[] = $sq;
			}
	    }
	    elseif ($hdist > 0 && $vdelta == 0) {
			for ($i = 0; $i < $hdist; $i++) {
			    $sq = $hdelta > 0 ? self::square_right_of($sq) : self::square_left_of($sq);
			    $squares[] = $sq;
			}
	    }
	    elseif ($hdist == $vdist) {
			for ($i = 0; $i < $hdist; $i++) {
			    $tsq 	= $hdelta > 0 ? self::square_right_of($sq) : self::square_left_of($sq);
			    $sq 	= $vdelta > 0 ? self::square_up_from($tsq) : self::square_down_from($tsq);
			    $squares[] = $sq;
			}
	    }
	    
	    return $squares;  	
  	}
  	
  	
  	
  	
  	/**
  	 * Takes a single scalar argument containing the square to retrieve the piece
  	 * from. Returns the piece on that square, or  false otherwise.
  	 * 
  	 * @param string $sq square position
  	 * @return Piece|bool piece on that square or false otherwise
  	 */
  	public function get_piece_at($sq){
  		#If $sq is not a valid square
	    if(self::square_is_valid($sq) == false) {
			return false;
	    }
	    
	    #Convert square position into coordinates
	    list($x, $y) = self::_get_square_coords($sq);
	    
	    return $this->board_arr[$y][$x]['piece']; 	
  	}
  	
  	
  	
  	
  	/**
  	 * Takes two scalar arguments: the square whose piece to set, and a scalar
  	 * representing the piece to place there. Usually this will be a subclass of
  	 * Piece, but could be something else if the board is being used
  	 * stand-alone. See Piece class description for more information on
  	 * using other things as pieces. Sets the piece at that square if the square is
  	 * valid, and return false otherwise.
  	 * 
  	 * @param string $sq square position
  	 * @param Piece piece to set on that square
  	 * @return bool returns false if square is not valid
  	 */
  	public function set_piece_at($sq, $piece){
  		#If $sq is not a valid square
	    if(self::square_is_valid($sq) == false) {
			return false;
	    }
	    
	    #Convert square position into coordinates
	    list($x, $y) = self::_get_square_coords($sq);

	    #Set the piece on the board
	    $this->board_arr[$y][$x]['piece'] = $piece;  		
  	}
  	
  	
  	
  	
  	/**
  	 * Takes two scalar arguments, valid squares defining the endpoints of a line
  	 * on the Board. Returns true if there are no pieces on either of the
  	 * endpoints, or on any of the intervening squares. Returns false if the line
  	 * is blocked by one or more pieces, and NULL if the two squares do not
  	 * define endpoints of a line. In the case where both squares are equal, will
  	 * return true if the square is empty and false otherwise.
  	 * 
  	 * @param string $sq1 square position 1
  	 * @param string $sq2 square position 2
  	 * @return bool
  	 */
  	public function line_is_open($sq1, $sq2){
		#If $sq is not a valid square
	    if (self::square_is_valid($sq1) == false || self::square_is_valid($sq2)  == false) {
			return NULL;
	    }

	    list($x1, $y1) = self::_get_square_coords($sq1);
	    list($x2, $y2) = self::_get_square_coords($sq2);
	    $hdist = abs($x2 - $x1);
	    $vdist = abs($y2 - $y1);
	    if($hdist != 0 && $vdist != 0 && $hdist != $vdist)
	    	return NULL;
	    $hdelta = $hdist ? $hdist / ($x2 - $x1) : 0;
	    $vdelta = $vdist ? $vdist / ($y2 - $y1) : 0;
	    $xcurr = $x1;
	    $ycurr = $y1;
	    if (($hdist == 0) && ($hdist == $vdist)) {
			if ($this->board_arr[$ycurr][$xcurr]['piece'] !== NULL)
				return false ;
			return true;
	    }
	    while(($xcurr != $x2) || ($ycurr != $y2)) {
			if ($this->board_arr[$ycurr][$xcurr]['piece'] !== NULL)
				return false ;
			$xcurr += $hdelta;
			$ycurr += $vdelta;
	    }
	    
	    return true;  	
  	}
 
  	
  
  
  
  
  }
