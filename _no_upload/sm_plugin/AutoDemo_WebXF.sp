#include <sourcemod>
#include <AutoDemo>
#include <ripext>

#pragma newdecls required
#pragma semicolon 1

char g_szApiPath[64],
    g_szApiKey[33];

int g_iServerId;

HTTPClient g_httpApiClient;

public Plugin myinfo = 
{
    name = "[AutoDemo] XenForo notifier",
    author = "West",
    description = "Notifies XenForo when demo recording stops",
    version = "0.0.1",
    url = "https://github.com/West14"
};

public void OnPluginStart()
{
    LoadConfiguration();

    g_httpApiClient = new HTTPClient(g_szApiPath);
    g_httpApiClient.SetHeader("XF-Api-Key", g_szApiKey);
}

public void DemoRec_OnRecordStop(const char[] szDemoId)
{
    char url[32];
    JSONObject data = new JSONObject();

    data.SetInt("server_id", g_iServerId);
    data.SetString("demo_id", szDemoId);

    Format(url, sizeof(url), "wsmad-servers/%i/new-demo", g_iServerId);

    DataPack hPack = new DataPack();
    hPack.WriteString(szDemoId);

    g_httpApiClient.Post(url, data, OnNewDemoResponse, hPack);

    delete data;
}

public void OnNewDemoResponse(HTTPResponse response, DataPack hPack)
{
    if (response.Status != HTTPStatus_OK)
    {
        PrintToServer("[AutoDemo_WebXF] API returned HTTP error %i", response.Status);
        return;
    }

    if (response.Data == null)
    {
        PrintToServer("[AutoDemo_WebXF] API returned invalid JSON response.");
        return;
    }

    char szDemoId[36];
    hPack.Reset();
    hPack.ReadString(szDemoId, sizeof(szDemoId));
    hPack.Close();

    PrintToServer("[AutoDemo_WebXF] Demo \"%s\" successfully submitted.", szDemoId);
}

public void LoadConfiguration()
{
    char path[PLATFORM_MAX_PATH + 1];
    
    BuildPath(Path_SM, path, sizeof(path), "configs/xenforo.json");

    JSONObject conf = JSONObject.FromFile(path);
    
    conf.GetString("ApiPath", g_szApiPath, sizeof(g_szApiPath));
    conf.GetString("ApiKey", g_szApiKey, sizeof(g_szApiKey));
    g_iServerId = conf.GetInt("ServerID");

    delete conf;
}

