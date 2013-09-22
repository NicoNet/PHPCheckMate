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
 


  class MoveListEntry{
  
    const __MOVE_CAPTURE__			= 0x01;
  	const __MOVE_CASTLE_SHORT__		= 0x02;
  	const __MOVE_CASTLE_LONG__		= 0x04;
  	const __MOVE_EN_PASSANT__		= 0x08;
  	const __MOVE_PROMOTE__			= 0x10;
  
  	protected $move_num 	= 0;
    protected $piece_ref 	= NULL;
    protected $from_sq 		= '';
    protected $dest_sq 		= '';
    protected $flags 		= 0x0;
    protected $promoted_to 	= NULL;
    protected $promoted_hash;
  
    
    
    
    /**
     * Constructs a new MoveListEntry with the provided parameters. Requires four
     * scalar parameters containing move number, piece, start square and destination
     * square. Optionally takes a fifth parameter containing flags for the entry.
     * 
     * @param integer $move_num move number
     * @param Piece $r_piece moved Piece object
     * @param string $from start square
     * @param string $dest destination square
     * @param integer $flags status of the piece. See class constants.
     * @return MoveListEntry
     */
    function __construct($move_num, $r_piece, $from, $dest, $flags = false){
		$this->move_num 	= $move_num;
		$this->piece_ref 	= $r_piece;
		$this->from_sq 		= $from;
		$this->dest_sq 		= $dest;
		$this->flags 		= $flags !== false ? $flags & 0x1f : 0x0;    
    }
    
    
    
    
    /**
     * Clones the MoveListEntry allowing to work on a identical MoveListEntry object.
     * 
     * @return MoveListEntry
     */
    function __clone(){
    	#Clone the piece
    	$this->piece_ref = clone $this->piece_ref;
    }
    
    
    
    
    /**
     * Sets the hash of the Piece if there was a promotion on that move
     * 
     * @param string $hash hash identifier of the Piece object
     */
    function set_promoted_hash($hash){
    	$this->promoted_hash = $hash;
    }
    
    
    
    
    /**
     * Returns the hash identifier of the promoted Piece that was created if there was a promotion on
     * that move.
     * 
     * @return string hash of the promoted Piece 
     */
    function get_promoted_hash(){
    	return $this->promoted_hash;
    }
    
    
    
    
    /**
     * Returns the move number this entry was constructed with.
     * 
     * @return integer move number at creation
     */
    public function get_move_num(){
    	return $this->move_num; 
	}
  	
	
	
	
	/**
	 * Returns the piece reference this entry was constructed with.
	 * 
	 * @return Piece
	 */
  	public function get_piece(){
    	return $this->piece_ref; 
	}
  	
	
	
	
	/**
	 * Returns the start square this entry was constructed with.
	 * 
	 * @return string
	 */
  	public function get_start_square(){
    	return $this->from_sq; 
	}
  	
	
	
	
	/**
	 * Returns the destination square this entry was constructed with.
	 * 
	 * @return string
	 */
  	public function get_dest_square(){
    	return $this->dest_sq;
	}
  	
	
	
	
	/**
	 * Returns true if the entry was recorded as a capture
	 * 
	 * @return bool
	 */
  	public function is_capture(){
    	return (bool)($this->flags & self::__MOVE_CAPTURE__ );
	}
  	
	
	
	
	/**
	 * Returns true if the entry was recorded as a short (kingside) castle.
	 * 
	 * @return bool
	 */
  	public function is_short_castle(){
    	return (bool)($this->flags & self::__MOVE_CASTLE_SHORT__);
	}
  	
	
	
	
	/**
	 * Returns true if the entry was recorded as a long (queenside) castle.
	 * 
	 * @return bool
	 */
  	public function is_long_castle(){
    	return (bool)($this->flags & self::__MOVE_CASTLE_LONG__);
	}
  	
	
	
	
	/**
	 * Returns true if the entry was recorded as an 'en passant' capture.
	 *  is_capture() will also return true in this case.
	 *  
	 *  @return bool
	 */
  	public function is_en_passant(){
    	return (bool)($this->flags & self::__MOVE_EN_PASSANT__);
	}
  	
	
	
	
	/**
	 * Returns true if the entry is recorded as a promotion
	 * 
	 * @return bool
	 */
  	public function is_promotion(){
    	return (bool)($this->flags & self::__MOVE_PROMOTE__);
	}
  	
	
	
	
	/**
	 * Returns the subclass name of the piece was promoted to
	 * 
	 * @return string
	 */
  	public function get_promoted_to(){
    	return $this->promoted_to;
	}
  	
	
	
	
	/**
	 * Sets the subclass name of the piece was promoted to.
	 * 
	 * @param string subclass name of the new piece
	 */
  	public function set_promoted_to($new_piece){
    	$this->promoted_to = $new_piece;
	}
  	
  	
  }