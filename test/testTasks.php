<?php

declare (strict_types=1);
require_once __DIR__ .'/../src/tasks.php';
/**
 * Funktion för att testa alla aktiviteter
 * @return string html-sträng med resultatet av alla tester
 */
function allaTaskTester(): string {
// Kom ihåg att lägga till alla testfunktioner
    $retur = "<h1>Testar alla uppgiftsfunktioner</h1>";
    $retur .= test_HamtaEnUppgift();
    $retur .= test_HamtaUppgifterSida();
    $retur .= test_RaderaUppgift();
    $retur .= test_SparaUppgift();
    $retur .= test_UppdateraUppgifter();
    return $retur;
}

/**
 * Funktion för att testa en enskild funktion
 * @param string $funktion namnet (utan test_) på funktionen som ska testas
 * @return string html-sträng med information om resultatet av testen eller att testet inte fanns
 */
function testTaskFunction(string $funktion): string {
    if (function_exists("test_$funktion")) {
        return call_user_func("test_$funktion");
    } else {
        return "<p class='error'>Funktionen $funktion kan inte testas.</p>";
    }
}

/**
 * Tester för funktionen hämta uppgifter för ett angivet sidnummer
 * @return string html-sträng med alla resultat för testerna 
 */
function test_HamtaUppgifterSida(): string {
    $retur = "<h2>test_HamtaUppgifterSida</h2>";
    try {
    // Testa hämta felaktigt sidnummer (-1) => 400
        $svar = hamtaSida(-1);
        if($svar->getStatus()===400) {
            $retur .= "<p class='ok'> Hämta felaktigt sidnummer (-1) gav förväntat svar 400";
        } else {
            $retur .= "<p class='error'> Hämta felaktigt sidnummer (-1) gav {$svar->getStatus()} "
            . "istället för föväntat svar 400</p>"; 
        }
        
    // Testa hämta giltigt sidnummer (1) => 200 + rätt egenskaper
    $svar = hamtaSida(1);
    if($svar->getStatus() !== 200){
         $retur .= "<p class='ok'> Hämta giltigt sidnummer (1) gav {$svar->getStatus()}"
         . "istället för förväntat svar 200</p>";
    } else {
        $retur .= "<p class='ok'> Hämta giltigt sidnummer (1) gav förväntat svar 200</p>";
        $result=$svar->getContent()->tasks;
        foreach ($result as $task) {
            if (!isset($task->id)) {
                $retur .= "<p class='error'> Egenskapen id saknas</p>";
                break;
            }
            if (!isset($task->activityId)) {
                $retur .= "<p class='error'> Egenskapen activityId saknas</p>";
                break;
            }
            if (!isset($task->activity)) {
                $retur .= "<p class='error'> Egenskapen activity saknas</p>";
                break;
            }
            if (!isset($task->date)) {
                $retur .= "<p class='error'> Egenskapen date saknas</p>";
                break;
            }
            if (!isset($task->time)) {
                $retur .= "<p class='error'> Egenskapen time saknas</p>";
                break;
            }
        }
    }
    // Testa hämta för stor sidnr => 200 + tom array
    $svar= hamtaSida(100);
    if ($svar->getStatus() !==200) {
         $retur .= "<p class='ok'> Hämta för stort sidnummer (100) gav {$svar->getStatus()}"
         . "istället för förväntat svar 200</p>";
    } else {
       $retur .= "<p class='ok'> Hämta giltigt sidnummer (100) gav förväntat svar 200</p>";
       $resultat=$svar->getContent()->tasks;
       if(!$resultat===[]) {
           $retur .= "<p class='error'>Hämta för stort sidnummer ska innehålla en tom array för tasks<br>"
                   . print_r($resultat, true) . " <br>returnerades</p>";
       }
    }
    } catch (Exception $ex) {

    }
    catch (Exception $ex){ 
        $retur .= "<p class='ok'>Testar hämta alla uppgifter på en sida</p>";
    }
    return $retur;
}

/**
 * Test för funktionen hämta uppgifter mellan angivna datum
 * @return string html-sträng med alla resultat för testerna
 */
function test_HamtaAllaUppgifterDatum(): string {
    $retur = "<h2>test_HamtaAllaUppgifterDatum</h2>";
    // Testa fel ordning på datum
    $datum1 = new DateTimeImmutable();
    $datum2 = new DateTime("yesterday");
    $svar = hamtaDatum($datum1, $datum2);
    if ($svar->getStatus()===400) {
        $retur .= "<p class='ok'> Hämta fel ordning på datum gav förväntat svar 400</p>";
    } else {
        $retur .= "<p class='error'> Hämta fel ordning på datum gav {$svar->getStatus()}"
        . "istället för förväntat svar 400</p>";
    }
    
    // Testa datum utan poster => 200 och tom array för tasls
    $datum1 = new DateTimeImmutable("1970-01-01");
    $datum2 = new DateTimeImmutable("1970-01-01");
    $svar = hamtaDatum($datum1, $datum2);
    if ($svar->getStatus() !==200) {
           $retur .= "<p class='ok'> Hämta för stort sidnummer (100) gav {$svar->getStatus()}"
           . "istället för förväntat svar 200</p>";
      } else {
         $retur .= "<p class='ok'> Hämta datum (1970-01-01 -- 1970-01-01) {$svar->getStatus()}"
         . " gav förväntat svar 200 </p>";
         $resultat=$svar->getContent()->tasks;
         if(!$resultat===[]) {
             $retur .= "<p class='error'>Hämta datum (1970-01-01 -- 1970-01-01) ska innehålla en tom array för tasks<br>"
                     . print_r($resultat, true) . " <br>returnerades</p>";
         }
      }
    // Testar giltiga datum med poster => 200 och giltiga egenskaper
    $datum1 = new DateTimeImmutable("1970-01-01");
    $datum2 = new DateTimeImmutable();
    $svar = hamtaDatum($datum1, $datum2);
    if($svar->getStatus() !== 200){
         $retur .= "<p class='error'> Hämta poster för datum (1970-01-01 -- {$datum2->format('Y-m-d')} "
         ."{$svar->getStatus()} istället för förväntat svar 200 </p>"
         . "istället för förväntat svar 200</p>";
    } else {
        $retur .= "<p class='ok'> Hämta poster för datum (1970-01-01 -- {$datum2->format('Y-m-d')} "
         ."{$svar->getStatus()} istället för förväntat svar 200 </p>";
        $result=$svar->getContent()->tasks;
        foreach ($result as $task) {
            if (!isset($task->id)) {
                $retur .= "<p class='error'> Egenskapen id saknas</p>";
                break;
            }
            if (!isset($task->activityId)) {
                $retur .= "<p class='error'> Egenskapen activityId saknas</p>";
                break;
            }
            if (!isset($task->activity)) {
                $retur .= "<p class='error'> Egenskapen activity saknas</p>";
                break;
            }
            if (!isset($task->date)) {
                $retur .= "<p class='error'> Egenskapen date saknas</p>";
                break;
            }
            if (!isset($task->time)) {
                $retur .= "<p class='error'> Egenskapen time saknas</p>";
                break;
            }
        }
    }    
    return $retur;
}

/**
 * Test av funktionen hämta enskild uppgift
 * @return string html-sträng med alla resultat för testerna
 */
function test_HamtaEnUppgift(): string {
    $retur = "<h2>test_HamtaEnUppgift</h2>";
    $retur .= "<p class='ok'>Testar hämta en uppgift</p>";
    return $retur;
}

/**
 * Test för funktionen spara uppgift
 * @return string html-sträng med alla resultat för testerna
 */
function test_SparaUppgift(): string {
    $retur = "<h2>test_SparaUppgift</h2>";
    try {
    // Testa allt OK
    $igar = new DateTimeImmutable("yesterday");
    $imorgon = new DateTimeImmutable("tomorrow");
    
    $postData=["date"=>$igar->format('Y-m-d'),
            "time"=>"05:00",
            "activityId"=>1,
            "description"=>"Hurra vad bra"];
    $db = connectDB();
    $db -> beginTransaction();
    $svar = sparaNyUppgift($postData);
    if($svar->getStatus()===200) {
        $retur .="<p class='ok'>Spara ny uppgift lyckades</p>";
    } else {
        $retur .="<p class='error'>Spara ny uppgift misslyckades {$svar->getStatus()}"
            ."returnerades istället för förväntat 200</p>";
    }
    $db->rollBack();
    // Testa felaktig datum (i morgon) => 400
    $postData["date"]=$imorgon->format('Y-m-d');
    $db -> beginTransaction();
    $svar = sparaNyUppgift($postData);
    if($svar->getStatus()===400) {
        $retur .="<p class='ok'>Spara ny uppgift misslyckades som förväntat</p>";
    } else {
        $retur .="<p class='error'>Spara ny uppgift misslyckades {$svar->getStatus()}"
            ."returnerades istället för förväntat 400</p>";
    }
    $db->rollBack();
    
    // Testa felaktig datumformat => 400
    $postData["date"]=$imorgon->format('d.m.Y');
    $db -> beginTransaction();
    $svar = sparaNyUppgift($postData);
    if($svar->getStatus()===400) {
        $retur .="<p class='ok'>Spara ny uppgift misslyckades som förväntat (felaktigt datumformat)</p>";
    } else {
        $retur .="<p class='error'>Spara ny uppgift med felaktigt datumformat"
             ."returnerade {$svar->getStatus()}istället för förväntat 400</p>";
    }
    $db->rollBack();
    // Testa datum saknas => 400
    unset($postData["date"]);
    $db -> beginTransaction();
    $svar = sparaNyUppgift($postData);
    if($svar->getStatus()===400) {
        $retur .="<p class='ok'>Spara ny uppgift misslyckades som förväntat (datum saknas)</p>";
    } else {
        $retur .="<p class='error'>Spara ny uppgift utan datum"
             ."returnerade {$svar->getStatus()}istället för förväntat 400</p>";
    }
    $db->rollBack();
    // Testa felaktig tid (12 timmar) => 400
    $db -> beginTransaction();
    $postData["date"]=$igar->format('Y-m-d');
    $postData["time"]="12:00";
    $svar = sparaNyUppgift($postData);
    if($svar->getStatus()===400) {
        $retur .="<p class='ok'>Spara ny uppgift misslyckades som förväntat (felaktigt tid 12:00)</p>";
    } else {
        $retur .="<p class='error'>Spara ny uppgift med felaktig tid (12:00) "
             ."returnerade {$svar->getStatus()}istället för förväntat 400</p>";
    }
    $db->rollBack();
    // Testa felaktigt tidsformat => 400
   $db -> beginTransaction();
   $postData["time"]="5_30";
   $svar = sparaNyUppgift($postData);
    if($svar->getStatus()===400) {
        $retur .="<p class='ok'>Spara ny uppgift misslyckades som förväntat (felaktigt tidsformat)</p>";
    } else {
        $retur .="<p class='error'>Spara ny uppgift med felaktig tidsformat) "
             ."returnerade {$svar->getStatus()}istället för förväntat 400</p>";
    }
    $db->rollBack();    
    // Testa tid saknas => 400
   $db -> beginTransaction();
   unset($postData["time"]);
   $svar = sparaNyUppgift($postData);
    if($svar->getStatus()===400) {
        $retur .="<p class='ok'>Spara ny uppgift misslyckades som förväntat (tid saknas)</p>";
    } else {
        $retur .="<p class='error'>Spara ny uppgift utan tid) "
             ."returnerade {$svar->getStatus()}istället för förväntat 400</p>";
    }
    $db->rollBack();     
    // Testa beskrivning saknas => 200
    unset($postData["description"]);
    $postData["time"]="3:15";
    $db -> beginTransaction();
    $svar = sparaNyUppgift($postData);
    if($svar->getStatus()===200) {
        $retur .="<p class='ok'>Spara ny uppgift utan beskrivning lyckades</p>";
    } else {
        $retur .="<p class='error'>Spara ny uppgift utan beskrivning "
             ."returnerade {$svar->getStatus()} istället för förväntat 200</p>";
    }
    $db->rollBack(); 
    // Testa aktivitetsid felaktigt (-1)
    $postData["activityId"]=-1;
    $db -> beginTransaction();
    $svar = sparaNyUppgift($postData);
    if($svar->getStatus()===400) {
        $retur .="<p class='ok'>Spara ny uppgift med felaktigt activityId (-1) misslyckades, som förväntat</p>";
    } else {
        $retur .="<p class='error'>Spara ny uppgift utan felaktigt activityId "
             ."returnerade {$svar->getStatus()} istället för förväntat 400</p>";
    }
    $db->rollBack(); 
    // Testa aktivitesid som saknas (100) => 400 
    $postData["activityId"]=-1;
    $db -> beginTransaction();
    $svar = sparaNyUppgift($postData);
    if($svar->getStatus()===400) {
        $retur .="<p class='ok'>Spara ny uppgift med felaktigt activityId (100) misslyckades, som förväntat</p>";
    } else {
        $retur .="<p class='error'>Spara ny uppgift utan felaktigt activityId (100)"
             ."returnerade {$svar->getStatus()} istället för förväntat 400</p>";
    }
    $db->rollBack(); 
    
        
    } catch (Exception $ex) {
        $retur .=$ex->getMessage();
    }
    
    return $retur;
}

/**
 * Test för funktionen uppdatera befintlig uppgift
 * @return string html-sträng med alla resultat för testerna
 */
function test_UppdateraUppgifter(): string {
    $retur = "<h2>test_UppdateraUppgifter</h2>";
    $retur .= "<p class='ok'>Testar uppdatera uppgift</p>";
    return $retur;
}

/**
 * Test för funktionen radera uppgift
 * @return string html-sträng med alla resultat för testerna
 */
function test_RaderaUppgift(): string {
    $retur = "<h2>test_RaderaUppgift</h2>";
    $retur .= "<p class='ok'>Testar radera uppgift</p>";
    return $retur;
}
