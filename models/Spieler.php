<?php

namespace app\models;

use yii\db\ActiveRecord;

class Spieler extends ActiveRecord
{
    /**
     * Gibt den Tabellen-Namen zurück.
     */
    public static function tableName()
    {
        return 'spieler'; // Tabellenname
    }
    
    /**
     * Validierungsregeln für das Model.
     */
    public function rules()
    {
        return [
            [['name', 'vorname', 'fullname', 'geburtsort', 'geburtsland'], 'string'],
            [['geburtstag'], 'date', 'format' => 'php:Y-m-d'], // Date im Format YYYY-MM-DD
            [['height', 'weight'], 'number'],
            [['spielfuss'], 'string', 'max' => 1], // Max. 1 Zeichen (z.B. 'L' oder 'R')
            [['homepage', 'facebook', 'instagram'], 'url'],
            [['nati1', 'nati2', 'nati3'], 'string', 'max' => 3], // IOC-Code max. 3 Zeichen
        ];
    }
    
    /**
     * Relationen zu anderen Tabellen.
     */
    public function getVereinSaison()
    {
        return $this->hasMany(SpielerVereinSaison::class, ['spielerID' => 'id']);
    }
    
    public function getFilteredVereinSaison($clubID, $year = null)
    {
        $query = $this->getVereinSaison()->andWhere(['vereinID' => $clubID]);
        
        if ($year !== null) {
            $query->andWhere(['<', 'von', ($year + 1) . '07'])
            ->andWhere(['>=', 'bis', $year . '06']);
        }
        
        return $query;
    }
    
    public function getLandWettbewerb()
    {
        return $this->hasMany(SpielerLandWettbewerb::class, ['spielerID' => 'id']);
    }
    
    public function getLandWettbewerbFiltered($clubID, $wettbewerbID, $jahr)
    {
        return $this->hasMany(SpielerLandWettbewerb::class, ['spielerID' => 'id'])
        ->andOnCondition([
            'spieler_land_wettbewerb.landID' => $clubID,
            'spieler_land_wettbewerb.wettbewerbID' => $wettbewerbID,
            'spieler_land_wettbewerb.jahr' => $jahr,
        ]);
    }
    
    public function getVereinVorSaison($month)
    {
        // Sicherstellen, dass $month ein gültiges Format hat (YYYYMM)
        if (!preg_match('/^\d{6}$/', $month)) {
            throw new InvalidArgumentException('Das Datum muss im Format YYYYMM übergeben werden.');
        }
        
        // Datenbankabfrage
        $query = (new \yii\db\Query())
        ->select(['vereinID', 'jugend'])
        ->from('spieler_verein_saison')
        ->where(['spielerID' => $this->id]) // Spieler filtern
        ->andWhere(['<=', 'bis', $month]) // Nur Einträge mit 'bis' <= $month
        ->orderBy(['bis' => SORT_DESC]) // Nach 'bis' absteigend sortieren
        ->limit(1); // Nur den letzten Eintrag
        
        // Abfrage ausführen
        $result = $query->one();
        
        // Wenn kein Eintrag gefunden wurde, Rückgabe null
        if (!$result) {
            return null;
        }
        
        // Rückgabe der gefundenen Werte als Array
        return [
            'vereinID' => $result['vereinID'],
            'jugend' => $result['jugend'],
        ];
    }
    
    public static function getZufallsId()
    {
        $query = Spieler::find()
        ->select(['id']) // Spalten auswählen
        ->orderBy(['rand()' => SORT_DESC]) // Sortieren
        ->limit(1)
        ->all();
        
        return $query[0]['id'];
    }
    
    public static function getGeburtstagskinder($datum) {
        $result = Spieler::find()
        ->select([
            'id',
            'nati1 AS land',
            'vorname',
            'name',
            '(YEAR(CURDATE()) - YEAR(geburtstag)) AS Age',
            'CURDATE() AS datum',
        ])
        ->where(['=', new \yii\db\Expression('SUBSTR(CURDATE(), 6, 5)'), new \yii\db\Expression('SUBSTR(geburtstag, 6, 5)')])
        ->orderBy(['rand()' => SORT_DESC])
        ->limit(5)
        ->asArray()
        ->all();
        
        return $result;
    }
    
    
}
?>