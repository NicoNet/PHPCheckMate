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

  class King extends Piece {
  
  
  	function reachable_squares(){
  		#Array of square where the piece can move to
  		$squares = array();
  		$csq 	= $this->get_current_square();
  		
	    $sq1 = Board::square_left_of($csq);
	    if ($sq1) {
			$squares[] = $sq1;
			$sq2 = Board::square_up_from($sq1);
			if($sq2)
				$squares[] = $sq2;
				
			$sq2 = Board::square_down_from($sq1);
			if($sq2)
				$squares[] = $sq2;
	    }
	    
	    $sq1 = Board::square_right_of($csq);
	    if ($sq1) {
			$squares[] = $sq1;
			
			$sq2 = Board::square_up_from($sq1);
			if($sq2)
				$squares[] = $sq2;
				
			$sq2 = Board::square_down_from($sq1);
			if($sq2)
				$squares[] = $sq2;
	    }
	    
	    $sq1 = Board::square_up_from($csq);
	    if ($sq1)
	    	$squares[] = $sq1;
	    	
	    $sq1 = Board::square_down_from($csq);
	    if($sq1)
	    	$squares[] = $sq1;
	    	
	    $sq1 = Board::add_horz_distance($csq, 2);
	    if($sq1 && !$this->moved())
	    	$squares[] = $sq1;
	    	
	    $sq1 = Board::add_horz_distance($csq, -2);
	    if($sq1 && !$this->moved())
	    	$squares[] = $sq1;

	    return $squares;  	
  	}
  
  	
  	
  	
	function captured() {
	   //"King can't be captured";
	}

	
	
	function set_captured($set) {
	    //"King can't be captured";
	}  
  
  
  }
