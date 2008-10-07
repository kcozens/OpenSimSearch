using System;
using System.Collections;
using System.Collections.Generic;
using System.Net;
using System.Net.Sockets;
using System.Reflection;
using System.Xml;
using OpenMetaverse;
using log4net;
using Nini.Config;
using Nwc.XmlRpc;
using OpenSim.Framework;
using OpenSim.Region.Interfaces;
using OpenSim.Region.Environment.Interfaces;
using OpenSim.Region.Environment.Scenes;
using OpenSim.Framework.Communications.Cache;

namespace OpenSimSearch.Modules.OpenSearch
{
    public class OpenSearchModule : IRegionModule
    {
        //
        // Log module
        //
        private static readonly ILog m_log = LogManager.GetLogger(MethodBase.GetCurrentMethod().DeclaringType);

        //
        // Module vars
        //
        private IConfigSource m_gConfig;
        private List<Scene> m_Scenes = new List<Scene>();
        private string m_SearchServer = "";
        private bool m_Enabled = true;

        public void Initialise(Scene scene, IConfigSource config)
        {
            if (!m_Enabled)
                return;

            IConfig searchConfig = config.Configs["Search"];

            if (m_Scenes.Count == 0) // First time
            {
                if (searchConfig == null)
                {
                    m_log.Info("[SEARCH] Not configured, disabling");
                    m_Enabled = false;
                    return;
                }
                m_SearchServer = searchConfig.GetString("SearchURL", "");
                if (m_SearchServer == "")
                {
                    m_log.Error("[SEARCH] No search server, disabling search");
                    m_Enabled = false;
                    return;
                }
                else
                {
                    m_log.Info("[SEARCH] Search module is activated");
                    m_Enabled = true;
                    return;
                }
            }

            if (!m_Scenes.Contains(scene))
                m_Scenes.Add(scene);

            m_gConfig = config;

            // Hook up events
            scene.EventManager.OnNewClient += OnNewClient;
        }

        public void PostInitialise()
        {
            if (!m_Enabled)
                return;
        }

        public void Close()
        {
        }

        public string Name
        {
            get { return "SearchModule"; }
        }

        public bool IsSharedModule
        {
            get { return true; }
        }

        /// New Client Event Handler
        private void OnNewClient(IClientAPI client)
        {
            // Subscribe to messages
            client.OnDirPlacesQuery += DirPlacesQuery;
        }

        //
        // Make external XMLRPC request
        //
        private Hashtable GenericXMLRPCRequest(Hashtable ReqParams, string method)
        {
            ArrayList SendParams = new ArrayList();
            SendParams.Add(ReqParams);

            // Send Request
            XmlRpcResponse Resp;
            try
            {
                XmlRpcRequest Req = new XmlRpcRequest(method, SendParams);
                Resp = Req.Send(m_SearchServer, 30000);
            }
            catch (WebException ex)
            {
                m_log.ErrorFormat("[SEARCH]: Unable to connect to Search " +
                        "Server {0}.  Exception {1}", m_SearchServer, ex);

                Hashtable ErrorHash = new Hashtable();
                ErrorHash["success"] = false;
                ErrorHash["errorMessage"] = "Unable to search at this time. ";
                ErrorHash["errorURI"] = "";

                return ErrorHash;
            }
            catch (SocketException ex)
            {
                m_log.ErrorFormat(
                        "[SEARCH]: Unable to connect to Search Server {0}. " +
                        "Exception {1}", m_SearchServer, ex);

                Hashtable ErrorHash = new Hashtable();
                ErrorHash["success"] = false;
                ErrorHash["errorMessage"] = "Unable to search at this time. ";
                ErrorHash["errorURI"] = "";

                return ErrorHash;
            }
            catch (XmlException ex)
            {
                m_log.ErrorFormat(
                        "[SEARCH]: Unable to connect to Search Server {0}. " +
                        "Exception {1}", m_SearchServer, ex);

                Hashtable ErrorHash = new Hashtable();
                ErrorHash["success"] = false;
                ErrorHash["errorMessage"] = "Unable to search at this time. ";
                ErrorHash["errorURI"] = "";

                return ErrorHash;
            }
            if (Resp.IsFault)
            {
                Hashtable ErrorHash = new Hashtable();
                ErrorHash["success"] = false;
                ErrorHash["errorMessage"] = "Unable to search at this time. ";
                ErrorHash["errorURI"] = "";
                return ErrorHash;
            }
            Hashtable RespData = (Hashtable)Resp.Value;

            return RespData;
        }

        protected void DirPlacesQuery(IClientAPI remoteClient, UUID queryID,
                string queryText, int queryFlags, int category, string simName,
                int queryStart)
        {
            Hashtable ReqHash = new Hashtable();
            ReqHash["text"] = queryText;
            ReqHash["flags"] = queryFlags;
            ReqHash["category"] = category;
            ReqHash["sim_name"] = simName;
            ReqHash["query_start"] = queryStart;

            Hashtable result = GenericXMLRPCRequest(ReqHash,
                    "dir_places_query");

            if (!Convert.ToBoolean(result["success"]))
            {
                remoteClient.SendAgentAlertMessage(
                        result["errorMessage"].ToString(), false);
            }

            ArrayList dataArray = (ArrayList)result["data"];

            int count = dataArray.Count;
            if (count > 100)
                count = 101;

            DirPlacesReplyData[] data = new DirPlacesReplyData[count];

            int i = 0;

            foreach (Object o in dataArray)
            {
            System.Console.WriteLine(o.GetType().ToString());
                Hashtable d = (Hashtable)o;

                data[i] = new DirPlacesReplyData();
                data[i].parcelID = new UUID(d["parcel_id"].ToString());
                data[i].name = d["name"].ToString();
                data[i].forSale = Convert.ToBoolean(d["for_sale"]);
                data[i].auction = Convert.ToBoolean(d["auction"]);
                data[i].dwell = Convert.ToSingle(d["dwell"]);
                i++;
                if (i >= count)
                    break;
            }

            remoteClient.SendDirPlacesReply(queryID, data);
        }
    }
}
