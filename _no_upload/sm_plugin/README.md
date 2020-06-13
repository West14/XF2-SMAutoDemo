## Description
This plugin notifies XenForo's REST API when demo recording ends.

## Requirements
- **[SourceMod 1.8](https://sm.alliedmods.net/)** or higher
- **[REST in Pawn](https://forums.alliedmods.net/showthread.php?t=298024)**
- **[sm-autodemo](https://github.com/CrazyHackGUT/sm-autodemo)**

## Building
For building you should use the SourceMod compiler.

To compile this plugin you should open your shell, go to your SourceMod `scripting` 
folder and compile the plugin by entering this command:


##### Windows:
```spcomp.exe AutoDemo_WebXF.sp -oAutoDemo_WebXF```

##### Linux / macOS:
```./spcomp64 AutoDemo_WebXF.sp -oAutoDemo_WebXF```

After compiling you will get `smx` file, which should be copied to your SourceMod 
`plugins` folder.

## Installation
To install the plugin you should copy `smx` file, which can be downloaded 
from the [Releases](https://github.com/West14/XF2-SMAutoDemo/releases) 
page or you may build it manually (see [Building](#building)).

> **:warning: Before you continue: you must setup the XenForo addon. Please follow instructions from the main README
> file which is located in this repository root. This will help you to configure the plugin properly.**

Then you need to create the config file for the plugin.
Go to your SourceMod directory on the target server, then open `configs` folder.
In this folder you should create a text file called `autodemo_xf.json`.
Fill it by this example:
```json
{
    "ApiPath": "https://<your XenForo address>/api",
    "ApiKey": "<Your API Key>",
    "ServerID": 0
}
```
You can get your ServerID parameter on the `Server list` page in your XenForo AdminCP. 

## Credits
- [Erik Minekus](https://forums.alliedmods.net/member.php?u=34668) for developing REST in Pawn.
- [CrazyHackGUT aka Kruzya](https://github.com/CrazyHackGUT) for developing the sm-autodemo plugin.