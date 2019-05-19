# Installation
Contr�le du Smart System Gardena via son API.  
  
### Les principes
  
Monitorer le statut des appareils connect�s Gardena (batterie...)  
D�marrer ou Arr�ter un arrosage.  
D�marrer ou Arr�ter une tondeuse.     
  
### Ajout des p�riph�riques
Cliquez sur "Configuration" / "Ajouter ou supprimer un p�riph�rique" / "Store eedomus" / "Gardena" / "Cr�er"  

  
*Voici les diff�rents champs � renseigner:*

* [Obligatoire] - Login  
* [Obligatoire] - Mot de passe 
* [Obligatoire] - Installer les capteurs d'un Water Control ?  
* [Obligatoire] - Nom de l'appareil Water Control  
* [Obligatoire] - Installer le statut d'une tondeuse SILENO ?  
* [Obligatoire] - Nom de la tondeuse   
  
Le capteur principal "Global Status" interroge le cloud Gardena. Il restitue les appareils Gardena connect�s. (D�tail dans le XML).    
    
L'actionneur "Commands" permet de contr�ler une tondeuse (SILENO) ou une valve d'arrosage (Water Control).  
Il faut pr�ciser le nom de l'appareil � controler dans la valeur "device" dans les URL appel�es (dans la configuration des valeurs du p�riph�rique).  
Pour l'arrosage, le temps par d�faut d'arrosage est � pr�ciser en VAR2. Par d�faut � 15mn.   
      
   
Influman 2019
therealinfluman@gmail.com  
[Paypal Me](https://www.paypal.me/influman "paypal.me")  


  



 

 

  


