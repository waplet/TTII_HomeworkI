Exercise:
create a currency conversion tool using the given code mock-up (http://estudijas.lu.lv/pluginfile.php/280908/mod_assign/intro/homework1.zip).
Currency rates have to be loaded from the Bank of Latvia (www.bank.lv) or European Central bank (www.ecb.info) web sites. 

The attached script is a mock-up of final code. It separates business logic from presentation:
- main logic (accessing currency rate file, conversion, preparation of output data) is carried out in index.php
- view.php is loaded as the last instruction of index.php
- presenation logic (HTML code + little analysis of variable values and output code) happens in "view.php"

Main tasks that have to be accomplished

- analyze user input data, perform conversion only if all mandatory fields are filled and data corresponds to expected type (e.g. amount can be recognized as a decimal value)
- download the currency rate file from the remote server (don't forget that the service may be unavailable, prepare a specific message for this case!)
- parse the currency rate documet in order to find the needed currency
- do conversion and round the result to 2 decimals

The variables that can be passed from index.php into view.php, are:
- $result_status (empty string, "success", "error): show on screen in if non-empty
- $result_message (a string to be displayed to the user - either error message or success explanation)
- $target_currencies (list of popular currencies, you can implement dynamic filling of this array) 

Additional information


The ECB currency rates can be found here: http://www.ecb.europa.eu/stats/exchange/eurofxref/html/index.en.html (remember, amount has to be divided by the rate to get the euro value)

The Bank of Latvia rates can be found here: http://www.bank.lv/vk/xml.xml (the amount has to be multiplied  with the rate)

The simplest XML data extractor is SimpleXML library, available in standard PHP bundle. . However, you need to know some XPath (see here: http://msdn.microsoft.com/en-us/library/ms256086.aspx)

Additional assignments for the i-option / grade 10.

To get the maximum score, include an option to convert the amount into Troy ounces (of gold). Gold exchange rate can be downloaded from http://www.goldfixing.com/today'sprices.htm where the data is available at this URL: http://www.goldfixing.com/vars/goldfixing.vars

Students who submit their work by the end of Thursday, September 26, get an extra point for that.
/**** Answers ****/

- analyzed input data, only good data afterwards is being sent to processing, although $_GET variables still are in use.
- currency rates were downloaded through simplexml_load_file() function, but gold rates where downloaded through file_get_contents(), and then parsed with regexp, to get out data of gold price.
- parsing was done through SimpleXML xpath() function, which were suggested by a teacher
- All the conversions were done and results rounded to 2 decimals using number_format() function, although i've got lot trouble with casting type of variables.
- Also the exchange rates for gold was added, how much ounces you could get with that amount of money.

Little editing was made to view.php due to lack of some attributes needed to php could access some input field values.

Māris Jankovkis, mj12015
