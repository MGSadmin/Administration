#!/bin/bash

# Script pour gérer la queue de l'application Administration
# Usage: ./queue-manager.sh [start|stop|restart|status|sync|database]

ARTISAN_PATH="/var/www/administration/artisan"
ENV_PATH="/var/www/administration/.env"
LOG_PATH="/var/www/administration/storage/logs/queue.log"

case "$1" in
    start)
        echo "🚀 Démarrage du queue worker..."
        nohup php $ARTISAN_PATH queue:work --daemon --tries=3 > $LOG_PATH 2>&1 &
        echo "✅ Queue worker démarré (PID: $!)"
        echo "📋 Logs: tail -f $LOG_PATH"
        ;;
    
    stop)
        echo "🛑 Arrêt des queue workers..."
        pkill -f "queue:work"
        echo "✅ Queue workers arrêtés"
        ;;
    
    restart)
        echo "🔄 Redémarrage des queue workers..."
        php $ARTISAN_PATH queue:restart
        sleep 2
        $0 start
        ;;
    
    status)
        echo "📊 Statut des queue workers:"
        WORKERS=$(ps aux | grep "queue:work" | grep -v grep | wc -l)
        if [ $WORKERS -gt 0 ]; then
            echo "✅ $WORKERS worker(s) en cours d'exécution"
            ps aux | grep "queue:work" | grep -v grep
        else
            echo "❌ Aucun worker en cours d'exécution"
        fi
        
        echo ""
        echo "📊 Jobs en queue:"
        php $ARTISAN_PATH queue:monitor
        ;;
    
    sync)
        echo "⚡ Passage en mode SYNC (immédiat)..."
        sed -i 's/QUEUE_CONNECTION=database/QUEUE_CONNECTION=sync/' $ENV_PATH
        php $ARTISAN_PATH config:clear
        php $ARTISAN_PATH cache:clear
        echo "✅ Mode SYNC activé - Les notifications sont envoyées immédiatement"
        ;;
    
    database)
        echo "💾 Passage en mode DATABASE (avec queue)..."
        sed -i 's/QUEUE_CONNECTION=sync/QUEUE_CONNECTION=database/' $ENV_PATH
        php $ARTISAN_PATH config:clear
        php $ARTISAN_PATH cache:clear
        echo "✅ Mode DATABASE activé"
        echo "⚠️  N'oubliez pas de démarrer le queue worker: $0 start"
        ;;
    
    logs)
        echo "📋 Logs du queue worker (Ctrl+C pour quitter):"
        tail -f $LOG_PATH
        ;;
    
    flush)
        echo "🗑️  Suppression des jobs échoués..."
        php $ARTISAN_PATH queue:flush
        echo "✅ Jobs échoués supprimés"
        ;;
    
    failed)
        echo "❌ Jobs échoués:"
        php $ARTISAN_PATH queue:failed
        ;;
    
    retry)
        if [ -z "$2" ]; then
            echo "🔄 Réessai de tous les jobs échoués..."
            php $ARTISAN_PATH queue:retry all
        else
            echo "🔄 Réessai du job $2..."
            php $ARTISAN_PATH queue:retry $2
        fi
        echo "✅ Jobs remis en queue"
        ;;
    
    work-once)
        echo "⚡ Traitement d'un job..."
        php $ARTISAN_PATH queue:work --once --tries=1
        ;;
    
    *)
        echo "📖 Usage: $0 {start|stop|restart|status|sync|database|logs|flush|failed|retry [id]|work-once}"
        echo ""
        echo "Commandes disponibles:"
        echo "  start        - Démarrer le queue worker en arrière-plan"
        echo "  stop         - Arrêter tous les queue workers"
        echo "  restart      - Redémarrer les queue workers"
        echo "  status       - Voir le statut et les jobs en queue"
        echo "  sync         - Passer en mode SYNC (notifications immédiates)"
        echo "  database     - Passer en mode DATABASE (avec queue)"
        echo "  logs         - Voir les logs en temps réel"
        echo "  flush        - Supprimer tous les jobs échoués"
        echo "  failed       - Lister les jobs échoués"
        echo "  retry [id]   - Réessayer un ou tous les jobs échoués"
        echo "  work-once    - Traiter un seul job (pour test)"
        exit 1
        ;;
esac
