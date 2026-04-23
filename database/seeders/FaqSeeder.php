<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            [
                'question' => 'Com puc crear un compte a la botiga?',
                'answer' => 'Per crear un compte, fes clic a "Registre" a la part superior dreta i segueix el procés de registre. Necessitaràs introduir el teu nom, adreça de correu electrònic i contrasenya.',
            ],
            [
                'question' => 'He oblidat la meva contrasenya. Què puc fer?',
                'answer' => 'Fes clic a "He oblidat la contrasenya" a la pàgina d\'inici de sessió i introdueix el teu correu electrònic. Rebràs un enllaç per restablir la teva contrasenya.',
            ],
            [
                'question' => 'Com puc actualitzar la informació del meu perfil?',
                'answer' => 'Vés a "El Meu Compte" i selecciona "Editar Perfil" per fer canvis en la teva informació personal com el nom, adreça o telèfon.',
            ],
            [
                'question' => 'Quins mètodes de pagament accepteu?',
                'answer' => 'Acceptem targetes de crèdit/dèbit (Visa, Mastercard), PayPal i transferència bancària. Tots els pagaments es processen de manera segura.',
            ],
            [
                'question' => 'Quant temps tarda el procés d\'enviament?',
                'answer' => 'L\'enviament standard tarda entre 2 i 5 dies hàbils. També oferim opció d\'enviament exprés (24-48h) per un cost addicional. Els temps poden variar segons la ubicació.',
            ],
            [
                'question' => 'Com puc fer el seguiment del meu pedit?',
                'answer' => 'Un cop realitzat el pedit, rebràs un correu electrònic amb el número de seguiment. També pots consultar l\'estat del teu pedit a "Les Meves Comandes" dins del teu compte.',
            ],
            [
                'question' => 'Qual és la vostra política de devolucions?',
                'answer' => 'Acceptem devolucions en un termini de 30 dies des de la recepció del producte. El producte ha d\'estar en el seu estat original, sense usar i amb l\'embalatge original. Contacta\'ns per iniciar el procés de devolució.',
            ],
            [
                'question' => 'Els productes tenen garantia?',
                'answer' => 'Sí, tots els nostres productes inclouen garantia mínima de 2 anys contra defectes de fabricació. Alguns productes poden tenir garantia extensa, consulta la descripció del producte.',
            ],
            [
                'question' => 'Com puc contactar amb l\'atenció al client?',
                'answer' => 'Pots contactar-nos per correu electrònic a info@cerrajeria-abp.com, per telèfon al 93 123 45 67 o a través del formulari de contacte de la web. L\'atenció al client és de dilluns a divendres de 9:00 a 18:00.',
            ],
            [
                'question' => 'Ofereu instal·lació dels productes?',
                'answer' => 'Sí, muitos dels nostres productes inclouen opció d\'instal·lació professional. Pots seleccionar aquesta opció durant el procés de compra. També oferim serveis d\'instal·lació a domicili per productes addicionals.',
            ],
        ];

        foreach ($faqs as $faqData) {
            Faq::updateOrCreate(
                ['question' => $faqData['question']],
                $faqData
            );
        }
    }
}
