#! /bin/bash -
javac -encoding utf8 *.java
jar -cvmf manifestETRON.mf ETRON.jar *.class
