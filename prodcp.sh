# Statamic: Copy content files from production
echo "Copying files from server"
echo "------------------------"
USER=""
SERVER="" # 
FOLDER=""

echo "Copying from /site/content …"
echo "------------------------"
rsync -avh $USER@$SERVER:/home/master/applications/$FOLDER/public_html/site/content/ "$PWD"/site/content --delete

echo "Copying from /img …"
echo "------------------------"
rsync -avh $USER@$SERVER:/home/master/applications/$FOLDER/public_html/img/ "$PWD"/img --delete

echo "Copying from /assets …"
echo "------------------------"
rsync -avh $USER@$SERVER:/home/master/applications/$FOLDER/public_html/assets/ "$PWD"/assets --delete

echo "Copying from /settings/formsets …"
echo "------------------------"
rsync -avh $USER@$SERVER:/home/master/applications/$FOLDER/public_html/site/settings/formsets/ "$PWD"/site/settings/formsets --delete

echo "Copying from /site/storage/forms …"
echo "------------------------"
rsync -avh $USER@$SERVER:/home/master/applications/$FOLDER/public_html/site/storage/forms/ "$PWD"/site/storage/forms

echo "Copying from /site/users …"
echo "------------------------"
rsync -avh $USER@$SERVER:/home/master/applications/$FOLDER/public_html/site/users/ "$PWD"/site/users --delete
