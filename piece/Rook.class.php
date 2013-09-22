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

  include_once('Board.class.php');
  include_once('Piece.class.php');

  class Rook extends Piece {
  
  
  	function reachable_squares(){
  		#Array of square where the piece can move to
  		$squares = array();
  	
	    $csq 	= $this->get_current_square();
	    $x 		= Board::horz_distance("a4", $csq);
	    $y 		= Board::vert_distance("d1", $csq);
	    $row_start 	= 'a' . ($y + 1);
	    $row_end 	= 'h' . ($y + 1); 
	    $col_start 	= chr(ord('a') + $x) . '1';
	    $col_end 	= chr(ord('a') + $x) . '8';
	    $row = Board::squares_in_line($row_start, $row_end);
	    $col = Board::squares_in_line($col_start, $col_end);
	    $squares = array_merge($squares, $row, $col);
	    
	    #Removes current position square
	    $squares = preg_grep("/^$csq$/", $squares, PREG_GREP_INVERT);

	    return $squares;  	
  	}
  
  
  
  
  }
