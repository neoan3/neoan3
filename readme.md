# neoan3 PHP all purpose hybrid framework

[![Build Status](https://travis-ci.com/sroehrl/neoan3.svg?branch=master)](https://travis-ci.com/sroehrl/neoan3)
[![Test Coverage](https://api.codeclimate.com/v1/badges/a3c9336dfc658b8f62dd/test_coverage)](https://codeclimate.com/github/sroehrl/neoan3/test_coverage)
[![google page speed](https://img.shields.io/badge/100%25%20page%20speed-lighthouse-blue.svg?style=flat&logo=data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIAAAACACAYAAADDPmHLAAANJ0lEQVR4AezBQQEAMBACICut5IXza6EVAQIAAAAAAABp+7bdZ8fccdsGoig6S0maIE1WkDZbSp+sIp3bQEAK21W+QAB9B6REghCoRoKgHyAVlOQVTO5z5dBPI9oxZYa6xYHtx48BnTPUgHEcXyZJ8vOx4PofJfG9Inwri8Fg8PUIX/r9/scgCN4AU4SjJ6Rp+iqKot+4ucPfzlrrer3eUbrd7snpdDql0G63S6HVaj0pck/5HLBYBYnmJTA+vAch/V0YhjeK9EoFwAB0sGAzLN63wBzi4AFZ+bjBjch8DM8rnwE0m01BngTZaDR6AYyGOhRw4S8RyQD+7wAE+LgGRkMdyoYPeAXLcWwKBfndG4DsHYbDoYvjmAE8QwDyv8IwfA1MHqMNIeyDTz7OcZvNxu33+1vW67VEoAaQJInb7XZy3u3PyWTCAE4cgAAn7ws/Aay1n30BiEQRepfxeKyu/ruhSABCFEUM4MQBgAtg8hhtiA/02hfAfD6/F8BsNlMDEOH5APB1wABOHABmDWDyGG14LIA0TfMByEzd/K1Wq7/kb7db2RMwgCoHAJFXwPmQR36WZcLBx78gsiUCkS9fB0mS8B1AHQLQKC6Mq792AUAEA6hfANWXzwB0+eUHoItnALUOQBdfffl8B/CwAIIguADxPxI9JXjT+CBwzUADAZeGtbavgWOFQdSFwfmhBgK7B87/BEweow0Xi8XVcrl0ZYB71xa8IKsyDWDyGG1YswAoH0yn0wYweYw21AOgfAZA+QyA8hkAxevy6x8A5TMAimcAFM8AdOmUX6cAAEWfewB+CAOgfAbAABgA5TOAusEALinZK/9sAyAMgDAAymcADIABUD4DoHwGUHGms6Xr/mHvXL+autI47F81HdsuP4DVOoo6F2ds6+gMS+2MiGuKsNY4HW110To6oFVQbjZCkMsJN6vIxRCuIndCIEBCSEgIiISLheDnd/ZvwsHFMUBy9jaXyodnJWcfcOl5fu6z97t3cswzVN0xSwUNHsp8Mk/XqhboStkifSO9InCZvUcbzmkbPfS44yV1DU2T07UdgKhj0DJNUquHrlYs0uncZTqU/pri0t5wYCP++zYH01bob3lLCAdVtL2kYdv2GCAi6Rl+QVm1c/Rl3jIkb8iBIAMgsx+svk/QLNG9+jkyWae3AxBOHM4p0rV66Nz9JTqY/hq8U/lKcO6rgp/pp+cvacK5HYCQYXNMUW79PH2e6YX0sMgHv7m+ssaxO8ukNXhYKN3bAXiXgzlNwxwdzVhRiOeQry4AkO4XhLKk2UMuV2TJd7lc0R2Aup5Zis/xQnjo5KsIwL5VTuUsUYtxZjsAvFjHpzBdg+zIlq8IAMDxtcoFGne4wy4/KgNg6HtJx+96o06+ksQcFxk6R7cDEAz3ns7RoRuvQy6fp+tXsv+6l74v6KdSSSKJMVygiZ4AsF98GK6B3uWyV6rEh0u+vwAcubFAOSVNVFRURAUFBVSQn08dv9tPjtRLNOl0hlx+VAQA07sk7VLUyz+dOUlFumoqLCwkyAeVKUnUu3sngYnks+R22LcDAGSs9ilK1CypEB9Z8i9phkiSykir1a7J12ZnUePeXZDvI/bX5EqIJ/f4eCjkR34AbI7wy+cNwOG0nymj+BkVFxdD/roAPD5xdJ38vhgfrsST5Lbb3+8ATLjkbp9ffLjk/+X2DGl1tfTgwYO35OtSr9BzJn1NfuxOyKd+hhE9QXIiTbqc71x+pAYAc3yF+OiSn5JroVKpAtLXywcaDenj9mwoHwwwJr67+H4GAGVdfvHhkR+XtkzphV1UWloK6X4DUHXmFPXEbi7ftMp4fp54+ZEcAEPfDB2KQvng6A8e0kh6dPkbyi/64Qa17flYlg/8ywcxO8m092Maa6h/PwKAEf/xLG/IxYuQn5htp2KpCrI3lA9qjxwKTD47N7iK8ehBsgwO8suP9AB8V77IJ16F/GN3vHRFt0gPGj1U3zNDxtFpsoy7seWLXOzC2RyTaCM9O1dg8NC37Gc/y/CuiT/A+F7rq+ptJb/sQgp1xgQhH7CfGWL0/juFRkdHf7kBwMUPlXgIvPlonjqGXqi7mIy2gRd066cZypN8VT3I3iwA2pxsati76418RqDygTn2Q+rSSWSxWETJV70WUCVavnNyik7mePnFbyH/xN1lKm7y0ISLfzPn8PAwPXr0CFW9LeWDx/FfUG8g8nf7kR+zk4ZZm+n4EXybOELwywpAfoPnnYo/fGOFcurmhYgHeA5iWVkZJAckv+RqKrXH8MkHI6gbZGXgq+rJarXyy4+EANgcbvosc0UpXZh8bAbtHZ4WsYUbF4qampqopKQkYPn59++T/uCnQuSPMsy//5Q625/h2QwIAbf8sAfgx6dzEChcPEgtW8RePCHy0e0+efIEU7yA5YOqc2eoh8ncQj7YWD4D8oFlN/sz7txEAAD+XtEbAKdrir7I9AoXD249nhciHvT391NFRQUEByW/OC+XWj/5yCefCRYh37qbHf8pjjo7nhMe3Q8QAhXywx+AirZZ4eLB7Wox8p1OJx6pgqpe0PJBeXk5dSadFSofWNhxf/6PkI/xCJ6/iNtB9AXgH/lLQqQru31e8cBms1FdXR26fFXyMTXU6/XUbDBQz+kTQuVbGeYz8bJ8gId2yyGA1MgPQN/INLd0Jfio1oSLXz6eYPrw4UPIVSUf7ZgiNjc3A2qpq6X+P+wPUj7wLx+MYS3B0LAmX2ZsbCw6ApBbN8clXclvb65Q78gUd7kUTxuTJEm1fKDT6TBbeBOAlhZqLyok0ycfvpEfyyP/A7IxBm+lrZPf19cHgg0BbnWljB1KlA1AWAD+fm+JS7qyhp9XP8cl3+FwoMtG180lH7eM+vr6dfJBa2sr9V5IFiYfWE/9WSkfYNCKW1jkBsBsm6I4QeLBX7O95OTo+lFjl6t6HPIBbh3+5Puez9egJ9OBGCHyx9mxfc9H1Nfe/pZ8o9EIEILIDEB52yyXeCVSyyzX/F6e4vHKR4HIYDD4lS/TffXKRvLBxvJZ+zr5qwwV3vcrf2BggEwmE57eHnkB+E/lArd0GazmOV3qp3nV1dVC5OM8CkWbyceTO9sb9GTet0uIfAdrH7n89YbyAQa06AkiKgBf5i5xi5e5UzPHU+AR0e0D9CJbygdYzOlPOSdEvgPtJ49tKl8GPUFEBMA56aaD6SuqhSvhGfnX1tZyiwfY8YsBZCDysZDTXaQVIt8R8yuaYL2JEQHYRP7Q0BBACMIfgG7ztEK4euKzl1XLB5ju8coHGEAGKN8HOx6Ji+GSDyYQgJgPyNTUuKV8YDabyW63hzwA6y56TddLyBPCtapFrgCg++eVjxCh6w9UPkCtwZRwcnP5IAD5wFxWGoh87GGQQ6A6AKCK46Jj65Vq4cq9ebrWWd4AqBUP8PuY8wctHxivXhEi38mOR+9lBSwfryMjIwhBWAKAQZtq6UpajC94AoBNHWrly3N+VfJBnyZPiHwXw3rz+lbyZSAfoPaB4lfoA3C9akG1dCWj43x1/8bGRjXi5Tk/yr2q5GMdv7eyQoh8YEu9GKx81D8AQhDaAKSWL6oVrkSe/6sFFwnduJoAYM6vWj7o1j8NTj7wI3+SYf/mn2rky2B2IDF2KFE2AO4AfCstQh43h9NXRHxQAtO3YOVjzo+uX7V80N3SLES+G58e+td5tfIBlpF1jB1KlA2AOwAXS18JCcAfb3EHACD9VFNTE5B43C4w6kfXzyHft5HjWZsQ+W7Wbr+QxCMflUIdY4cSZQPANLCS54J/XcwXgH2rfJ7h5ZUvg+VTFIWwkrfpKh+WeVHr55UPurs6hcifwi0g5Zxq+fi3hzQAGr2HLkmvguKiH7CeIPjLEtGtYzlYBtJR5cP/ernYI0Y+dvKw15GvztBoUgJZVrGeP0tW9grGzieQLcnHOHtvZ+f+T/JZciQn+kjxvY7dva1avuAARD8YTKGw439Jl1++chuX//X8rWv7nPf8/7Vr9joNw2AU9XswwIA6MLPwVkhISLwKC1JHhPpDGZMhg58jUjaGvkLFuVuVOKRhws4dzhepSpzPvidqkvY8fAuQluBv4bPNKnzugSxAChZ3EeHDvMfAogVISFB6+HolbAHG0UL/Gj5kHb7eBFqAaQlKDd8CzJCgtPAtwFy06DHGosKfLUDbtmstxoIlKCp8wX6vEPqoDGDwlyULIFj8YsLngtZxjxD6qAxgAnc6qLMERYTPWCd6u4XQRyUJBx2WLoBQEDmHzy+7Otc7hBQqSZjEDc0cO0ugxc82fMb+pqcrCClURqHpBwY4aqDOEmQVvrYKn/7uIYyhMsU1E9jThAbWP2yWKoGC+Nfh85n20TlP9PORvPJ7qFwEk1uxfWZyaya+zQF63syBMCdpmuazqqovUdf1IYX2uYQY4x525/D4uePxcysE9x8D+CrajPBGj0/pG740KqZQLICxAMYCGAtgLICxAMYCGAtgLICxAMYCGAtgfgCQ6MTut+33AgAAAABJRU5ErkJggg==&colorA=555)](https://developers.google.com/speed/pagespeed/insights/?url=https%3A%2F%2Fneoan3.rocks%2Fgetting-started%2F)
![Discord](https://img.shields.io/discord/701820506671677580?label=discord&style=flat)
![Code Climate technical debt](https://img.shields.io/codeclimate/tech-debt/neoan3/neoan3)
![GitHub repo size](https://img.shields.io/github/repo-size/neoan3/neoan3)

Rapid development, light-weight, lightning-fast, beginner-friendly yet limitlessly powerful? 

neoan3 is all that and more. 

[See documentation](http://neoan3.rocks/getting-started/) (work in progress)

<h1>Version 3</h1>

## Give it 5 minutes

Yet another thing to learn? Here is our theory: Give it a shot! After 5 minutes you will be interested. 
After another 5 minutes you will start to understand. After another 5 minutes you will be writing your first application.


## Requirements

PHP ^7.4 
(as of version 2.0, we officially dropped 7.2+ support)

[composer](https://getcomposer.org/)


## Download

Easiest & fastest way is through the cli-tool:

`composer global require neoan3/neoan3`

## Installation

_via cli_

1. Run `neoan3 new app [base]`
2. Run `neoan3 develop`
3. that's it, you are good to go

_via fork or download_

You can also clone, fork or download this repository. 

(You might have to adjust the .htaccess file depending on your system)


MIT license [opensource](https://opensource.org/licenses/MIT)

Copyright 2018 [neoan](http://neoan.us) (Stefan Roehrl) 

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.


