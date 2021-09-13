#define PLUGIN_NAME "[AutoDemo] XenForo"

#include <sourcemod>
#include <AutoDemo>
#include <ripext>

#pragma newdecls required
#pragma semicolon 1

char 
    g_szApiKey[33],
    g_szBaseUrl[64];

int 
    g_iServerId;


public Plugin myinfo = 
{
    name        =  PLUGIN_NAME,
    url         = "https://github.com/West14",
    author      = "West",
    version     = "0.0.2",
    description = "Notifies XenForo when demo recording stops"
};

public void OnPluginStart()
{
    char szPath[PLATFORM_MAX_PATH];
    BuildPath(Path_SM, szPath, sizeof szPath, "configs/AutoDemo/xenforo.json");

    JSONObject hConf = JSONObject.FromFile(szPath);

    g_iServerId = hConf.GetInt("ServerID");

    hConf.GetString("ApiKey", g_szApiKey, sizeof g_szApiKey);
    hConf.GetString("BaseURL", g_szBaseUrl, sizeof g_szBaseUrl);

    delete hConf;
}

public void DemoRec_OnRecordStop(const char[] szDemoId)
{
    char szUrl[128];
    FormatEx(szUrl, sizeof szUrl, "%s/api/wsmad-servers/%i/new-demo", g_szBaseUrl, g_iServerId);

    DataPack hPack = new DataPack();
    JSONObject hData = new JSONObject();
    HTTPRequest hRequest = new HTTPRequest(szUrl);

    hData.SetString("demo_id", szDemoId);

    hPack.WriteString(szDemoId);

    hRequest.SetHeader("XF-Api-Key", g_szApiKey);
    hRequest.Post(hData, OnNewDemoResponse, hPack);

    delete hData;
}

void OnNewDemoResponse(HTTPResponse hResponse, DataPack hPack, const char[] szError)
{
    if(hResponse.Status == HTTPStatus_OK)
    {
        hPack.Reset();

        char szDemoID[64];
        hPack.ReadString(szDemoID, sizeof szDemoID);
        PrintToServer(PLUGIN_NAME...": Demo \"%s\" successfully submitted.", szDemoID);
    }
    else LogError("API returned invalid response: [%i][%s]", hResponse.Status, szError);

    delete hPack;
}