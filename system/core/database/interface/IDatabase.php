<?php

interface IDatabase
{
	/**
	 * 
	 * @return IRecordReader
	 * @param string $sql
	 */
	public function ExecuteReader($sql);
	/**
	 * 
	 * @return integer 
	 * @param object $sql
	 */
	public function ExecuteNonQuery($sql);
}
