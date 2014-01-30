#!/bin/python
# coding=utf-8
# Class BitShiftingBlindMySQL
# Syntax: Python
# Date: 01-2014
#
# ###### Python BitShiftingMySQL CLASS v0.1Beta ######
#
# Coder: Norberto Martínez a.k.a. kl4nx
# Contact: klanx.martinez@gmail.com
# Twitter: @klanx
#
# #################################################
# NOTES:
# --
# ...
# #################################################
# MODERATORS
# --
# ...
#=- TEST
# value = "test"
# bit = BitShiftingMySQL(value)

import mysql.connector

class BitShiftingMySQL:
    dataConnection = False
    userDB = ''
    passDB = ''
    portDB = '3306'
    hostDB = 'localhost'
    nameDB = 'test'
    position = 7
    substr_pos = 1
    substr_len = 1
    const = "0"
    alter_const = "1"
    binary = ""
    response = ""
    query_compare_template = "SELECT (ascii((substr('{0}',{1},{2}))) >> {3}) = {4};"
    query_consult_ascii = "SELECT b'{0}';"

    # init
    def __init__(self, value):
        if self.dataConnection==False:
            self.connect()
        print self.run(value)

    # run(value)
    def run(self, value):
        value.split()
        for chr in value:
            self.response += "/"+self.getVal(chr)
        return self.response

    # connect
    def connect(self):
        self.dataConnection= mysql.connector.connect(
            host=self.hostDB,
            user=self.userDB,
            passwd=self.passDB,
            db=self.nameDB,
            port=self.portDB
        )

    # query_exec(query)
    def query_exec(self,query):
        cur = self.dataConnection.cursor(buffered=True)
        cur.execute(query)
        return cur.fetchone()

    def reset(self):
        self.const = "0"
        self.alter_const = "1"
        self.binary = "0"
        self.position = 7
        
    # getval(value)
    def getVal(self, value):
        self.reset() # Reset
        while self.position>=0:
            query = self.query_compare_template.format(
                value,
                self.substr_pos,
                self.substr_len,
                self.position,
                int(self.binary+self.const,2)
            )
            response = self.query_exec(query)
            if response[0]==1:
                self.binary = self.binary + self.const
            else:
                self.binary = self.binary + self.alter_const
            self.position -= 1
        q = self.query_consult_ascii.format(
            self.binary
        )
        res = self.query_exec(q)
        # Enjoy :3
        return res[0]+"="+self.binary
