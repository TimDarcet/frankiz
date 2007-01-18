#! /bin/bash -
javac -encoding utf8 *.java
jar -cvmf manifestBITE.mf BITE.jar *.class
