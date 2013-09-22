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

  class Knight extends Piece {
  
  
  	function reachable_squares(){
  		#Array of square where the piece can move to
  		$squares = array();
  		$csq 	= $this->get_current_square();
  	
  	    $tsq = Board::add_vert_distance($csq, 2);
	    if($tsq) {
			$sq = Board::square_right_of($tsq);
			if($sq)
				$squares[] = $sq;
			$sq = Board::square_left_of($tsq);
			if($sq)
				$squares[] = $sq;
	    }
	    
	    $tsq = Board::add_vert_distance($csq, -2);
	    if($tsq) {
			$sq = Board::square_right_of($tsq);
			if($sq)
				$squares[] = $sq;
			$sq = Board::square_left_of($tsq);
			if($sq)
				$squares[] = $sq;
	    }
	    
	    $tsq = Board::add_horz_distance($csq, 2);
	    if($tsq) {
			$sq = Board::square_up_from($tsq);
			if($sq)
				$squares[] = $sq;
			$sq = Board::square_down_from($tsq);
			if($sq)
				$squares[] = $sq;
	    }
	    
	    $tsq = Board::add_horz_distance($csq, -2);
	    if($tsq) {
			$sq = Board::square_up_from($tsq);
			if($sq)
				$squares[] = $sq;
			$sq = Board::square_down_from($tsq);
			if($sq)
				$squares[] = $sq;
	    }

	    return $squares;  	
  	}
  
  
  
  
  }
