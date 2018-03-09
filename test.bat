:start
IF EXIST node_modules (
	echo dependencies installed
) ELSE (
	echo Need to install dependencies, please wait
	npm install
)
node server.js
Goto start
