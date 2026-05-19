#!/bin/bash
YEAR=`date +%Y`;
MONTH=`date +%m`;
DAY=`date +%d`;
#The root directory
ROOTDIR='/home/scheduler/dbackup/';

#"Lock" rootdir
LOCK=$ROOTDIR"lock.lck";
if [ ! -f "$LOCK" ]; then
        touch $LOCK;
        chattr +i $LOCK;
fi

#AVOID using mkdir -p
#check and create if necessary the year dir
DIR=$ROOTDIR$YEAR"/";
if [ ! -d "$DIR" ]; then
#       echo "Creating $DIR";
        mkdir $DIR;
fi
#Check and create if necessary the month dir
DIR=$DIR$MONTH"/";
if [ ! -d "$DIR" ]; then
#        echo "Creating $DIR";
        mkdir $DIR;
fi
#Check and create if necessary the day dir
DIR=$DIR$DAY"/";
if [ ! -d "$DIR" ]; then
#        echo "Creating $DIR";
        mkdir $DIR;
fi


SUPERUSERNAME="test";
SUPERPASS="test123";
DATE=`date +%d_%m_%Y_%H_%M_%S`;
CLEANDATE=`date "+%d-%m-%Y_%H:%M:%S"`;
DB="scheduler_production";
DBDATE=$DB"_"$DATE;
OUTPUT=$DIR$DBDATE".sql.gz";
/usr/bin/mysqldump -u SUPERUSERNAME -pSUPERPASS --routines --add-drop-table $DB | /bin/gzip > $OUTPUT;
echo -n "Successfully created "$OUTPUT" backup.";
#echo "New backup, please check attached file periodically!" | /usr/bin/mutt -s "Scheduler DB backup "$CLEANDATE -a $OUTPUT -- mail@mail
.com