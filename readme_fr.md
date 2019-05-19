# Installation
Contrôle du Smart System Gardena via son API.  
  
### Les principes
  
Monitorer le statut des appareils connectés Gardena (batterie...)  
Démarrer ou Arrêter un arrosage.  
Démarrer ou Arrêter une tondeuse.     
  
### Ajout des périphériques
Cliquez sur "Configuration" / "Ajouter ou supprimer un périphérique" / "Store eedomus" / "Gardena" / "Créer"  

  
*Voici les différents champs à renseigner:*

* [Obligatoire] - Login  
* [Obligatoire] - Mot de passe 
* [Obligatoire] - Installer les capteurs d'un Water Control ?  
* [Obligatoire] - Nom de l'appareil Water Control  
* [Obligatoire] - Installer le statut d'une tondeuse SILENO ?  
* [Obligatoire] - Nom de la tondeuse   
  
Le capteur principal "Global Status" interroge le cloud Gardena. Il restitue les appareils Gardena connectés. (Détail dans le XML).    
    
L'actionneur "Commands" permet de contrôler une tondeuse (SILENO) ou une valve d'arrosage (Water Control).  
Il faut préciser le nom de l'appareil à controler dans la valeur "device" dans les URL appelées (dans la configuration des valeurs du périphérique).  
Pour l'arrosage, le temps par défaut d'arrosage est à préciser en VAR2. Par défaut à 15mn.   
      
   
Influman 2019
therealinfluman@gmail.com  
[Paypal Me](https://www.paypal.me/influman "paypal.me")  


  



 

 

  


