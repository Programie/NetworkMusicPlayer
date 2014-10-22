#! /bin/bash

TOOLSPATH=$(dirname $0)
ROOTPATH="$TOOLSPATH/.."

function require()
{
	which $1 > /dev/null 2>&1 || (echo "Command '$1' not found!"; exit 1)
}

require java

FROM_HOST="$1"
FROM_DATABASE="$2"
FROM_USERNAME="$3"
FROM_PASSWORD="$4"

if [ -f ${TOOLSPATH}/tools.conf ]; then
	. ${TOOLSPATH}/tools.conf

	if [ -n "$MYSQL_DIFF_HOST" ]; then
		FROM_HOST="$MYSQL_DIFF_HOST"
	fi
	if [ -n "$MYSQL_DIFF_DATABASE" ]; then
		FROM_DATABASE="$MYSQL_DIFF_DATABASE"
	fi
	if [ -n "$MYSQL_DIFF_USERNAME" ]; then
		FROM_USERNAME="$MYSQL_DIFF_USERNAME"
	fi
	if [ -n "$MYSQL_DIFF_PASSWORD" ]; then
		FROM_PASSWORD="$MYSQL_DIFF_PASSWORD"
	fi
fi

if [ -z "$FROM_HOST" ] || [ -z "$FROM_DATABASE" ]; then
	echo "Usage: $0 <host> <database> [<username>]"
	exit 1
fi

if [ -z "$FROM_USERNAME" ]; then
	FROM_USERNAME="$USER"
fi

if [ -z "$FROM_PASSWORD" ]; then
	echo -n "Password: "
	read -s FROM_PASSWORD
fi

TO_DSN=$(php ${TOOLSPATH}/getconfigvalue.php database.mainDb.dsn | sed 's/mysql:\(.*\)/\1/g')
TO_USERNAME="`php ${TOOLSPATH}/getconfigvalue.php database.mainDb.username`"
TO_PASSWORD="`php ${TOOLSPATH}/getconfigvalue.php database.mainDb.password`"

for FULLVAR in $(echo ${TO_DSN} | tr ";" "${IFS}"); do
	VAR=$(echo ${FULLVAR} | tr "=" "${IFS}")
	VAR=(${VAR})
	NAME=${VAR[0]}
	VALUE=${VAR[1]}

	case "${NAME}" in
		dbname)
			TO_DATABASE="${VALUE}"
		;;
		host)
			TO_HOST+=("${VALUE}")
		;;
	esac
done

java -jar ${TOOLSPATH}/mysql-diff.jar "jdbc:mysql://$FROM_HOST/$FROM_DATABASE?user=$FROM_USERNAME&password=$FROM_PASSWORD" "jdbc:mysql://$TO_HOST/$TO_DATABASE?user=$TO_USERNAME&password=$TO_PASSWORD"
