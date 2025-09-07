<?php
interface IRecordReader
{
	/**
	 * 
	 * @return Array 
	 */
    public function GetNextArray();
	/**
	 * 
	 * @return Array 
	 */
    public function GetNextAssoc();
}