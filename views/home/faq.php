<?php 
$pageTitle = 'FAQ - ECO-SNAP';
ob_start(); 
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h2 class="text-center fw-bold text-success mb-5">
                <i class="bi bi-question-circle"></i> Questions Fréquentes
            </h2>
            
            <div class="accordion" id="faqAccordion">
                <!-- Question 1 -->
                <div class="accordion-item mb-3 shadow-sm border-0">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                            <strong>Qu'est-ce que ECO-SNAP ?</strong>
                        </button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            ECO-SNAP est une plateforme web qui permet aux citoyens de signaler des dépôts sauvages d'ordures et de connecter automatiquement ces signalements avec les équipes de collecte disponibles dans la zone géographique concernée.
                        </div>
                    </div>
                </div>
                
                <!-- Question 2 -->
                <div class="accordion-item mb-3 shadow-sm border-0">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                            <strong>Comment créer un compte ?</strong>
                        </button>
                    </h2>
                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Cliquez sur "Inscription" dans le menu, remplissez le formulaire avec vos informations et choisissez votre type de compte :
                            <ul>
                                <li><strong>Habitant</strong> : Pour signaler des dépôts d'ordures</li>
                                <li><strong>Chauffeur/Équipe</strong> : Pour recevoir les signalements et les prendre en charge</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Question 3 -->
                <div class="accordion-item mb-3 shadow-sm border-0">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                            <strong>Comment fonctionne le filtrage des chauffeurs ?</strong>
                        </button>
                    </h2>
                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Lorsqu'un signalement est créé, le système recherche automatiquement les chauffeurs qui :
                            <ul>
                                <li>Travaillent dans la <strong>même zone géographique</strong> que le signalement</li>
                                <li>Ont un statut <strong>"actif"</strong></li>
                                <li>Ont déclaré <strong>travailler ce jour-là</strong> dans leur planning</li>
                            </ul>
                            Seuls ces chauffeurs reçoivent une notification du signalement.
                        </div>
                    </div>
                </div>
                
                <!-- Question 4 -->
                <div class="accordion-item mb-3 shadow-sm border-0">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                            <strong>Qu'est-ce que le planning de travail ?</strong>
                        </button>
                    </h2>
                    <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Le planning de travail est un calendrier que chaque chauffeur configure pour indiquer ses jours de travail. Cela permet au système de :
                            <ul>
                                <li>Notifier uniquement les chauffeurs qui travaillent le jour du signalement</li>
                                <li>Optimiser la répartition des interventions</li>
                                <li>Éviter de déranger les chauffeurs leurs jours de repos</li>
                            </ul>
                            Pour modifier votre planning : Connectez-vous → Mon planning
                        </div>
                    </div>
                </div>
                
                <!-- Question 5 -->
                <div class="accordion-item mb-3 shadow-sm border-0">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                            <strong>Comment recevoir les notifications en temps réel ?</strong>
                        </button>
                    </h2>
                    <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Les notifications sont automatiques ! Lorsque vous êtes connecté en tant que chauffeur :
                            <ul>
                                <li>Une connexion <strong>Server-Sent Events (SSE)</strong> est établie automatiquement</li>
                                <li>Quand un signalement correspond à votre zone et votre planning, vous recevez une notification instantanée</li>
                                <li>Un toast apparaît en haut à droite de l'écran</li>
                                <li>Un badge rouge sur l'icône de notifications indique le nombre de notifications non lues</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Question 6 -->
                <div class="accordion-item mb-3 shadow-sm border-0">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq6">
                            <strong>Comment prendre en charge un signalement ?</strong>
                        </button>
                    </h2>
                    <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <ol>
                                <li>Allez dans "Signalements disponibles" depuis votre dashboard chauffeur</li>
                                <li>Consultez la liste des signalements en attente dans votre zone</li>
                                <li>Cliquez sur "Prendre en charge" sur le signalement souhaité</li>
                                <li>Le statut du signalement passe à "Pris en charge"</li>
                                <li>Une fois le travail terminé, marquez-le comme "Terminé"</li>
                            </ol>
                        </div>
                    </div>
                </div>
                
                <!-- Question 7 -->
                <div class="accordion-item mb-3 shadow-sm border-0">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq7">
                            <strong>Puis-je ajouter une photo à mon signalement ?</strong>
                        </button>
                    </h2>
                    <div id="faq7" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <strong>Oui !</strong> La photo est même recommandée pour :
                            <ul>
                                <li>Montrer l'ampleur du dépôt d'ordures</li>
                                <li>Aider les équipes à identifier le lieu</li>
                                <li>Suivre l'évolution avant/après nettoyage</li>
                            </ul>
                            Formats acceptés : JPEG, PNG, GIF, WEBP (max 5MB)
                        </div>
                    </div>
                </div>
                
                <!-- Question 8 -->
                <div class="accordion-item mb-3 shadow-sm border-0">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq8">
                            <strong>La géolocalisation est-elle obligatoire ?</strong>
                        </button>
                    </h2>
                    <div id="faq8" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <strong>Non</strong>, la géolocalisation est optionnelle mais fortement recommandée. Elle permet de :
                            <ul>
                                <li>Positionner exactement le dépôt sur une carte</li>
                                <li>Aider les chauffeurs à trouver le lieu rapidement</li>
                                <li>Créer des statistiques précises par zone</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Question 9 -->
                <div class="accordion-item mb-3 shadow-sm border-0">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq9">
                            <strong>Comment changer ma zone d'intervention (chauffeur) ?</strong>
                        </button>
                    </h2>
                    <div id="faq9" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Actuellement, la zone d'intervention est définie à l'inscription. Pour la modifier, contactez l'administrateur ou créez un nouveau compte avec la bonne zone.
                        </div>
                    </div>
                </div>
                
                <!-- Question 10 -->
                <div class="accordion-item mb-3 shadow-sm border-0">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq10">
                            <strong>Le service est-il gratuit ?</strong>
                        </button>
                    </h2>
                    <div id="faq10" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <strong>Oui, 100% gratuit !</strong> ECO-SNAP est un service public gratuit pour tous les utilisateurs.
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-5">
                <p class="text-muted">Vous avez d'autres questions ?</p>
                <a href="<?= url('/contact') ?>" class="btn btn-success">
                    <i class="bi bi-envelope"></i> Contactez-nous
                </a>
            </div>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
require __DIR__ . '/../layout.php'; 
?>
