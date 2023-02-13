using System;
using System.Collections;
using System.Collections.Generic;
using System.Globalization;
using System.Net;
using System.Net.Sockets;
using System.Reflection;
using System.Xml;
using OpenMetaverse;
using log4net;
using Nini.Config;
using Nwc.XmlRpc;
using OpenSim.Framework;
using OpenSim.Region.Framework.Interfaces;
using OpenSim.Region.Framework.Scenes;
using OpenSim.Services.Interfaces;
using Mono.Addins;

using DirFindFlags = OpenMetaverse.DirectoryManager.DirFindFlags;

[assembly: Addin("OpenSimSearch", OpenSim.VersionInfo.VersionNumber + "0.4")]
[assembly: AddinDependency("OpenSim.Region.Framework", OpenSim.VersionInfo.VersionNumber)]
[assembly: AddinDescription("OpenSimSearch module.")]
[assembly: AddinAuthor("Unknown")]


namespace OpenSimSearch.Modules.OpenSearch
{
    [Extension(Path = "/OpenSim/RegionModules", NodeName = "RegionModule", Id = "OpenSimSearch")]
    public class OpenSearchModule : ISearchModule, ISharedRegionModule
    {
        //
        // Log module
        //
        private static readonly ILog m_log = LogManager.GetLogger(MethodBase.GetCurrentMethod().DeclaringType);

        //
        // Module vars
        //
        private List<Scene> m_Scenes = new();
        private string m_SearchServer = "";
        private bool m_Enabled = true;

        #region IRegionModuleBase implementation
        public void Initialise(IConfigSource config)
        {
            IConfig searchConfig = config.Configs["Search"];

            if (searchConfig is null)
            {
                m_Enabled = false;
                return;
            }
            if (searchConfig.GetString("Module", "OpenSimSearch") != "OpenSimSearch")
            {
                m_Enabled = false;
                return;
            }

            m_SearchServer = searchConfig.GetString("SearchURL", "");
            if (m_SearchServer == "")
            {
                m_Enabled = false;
                return;
            }

            m_log.Info("[SEARCH] OpenSimSearch module is active");
            m_Enabled = true;
        }

        public void AddRegion(Scene scene)
        {
            if (!m_Enabled)
                return;

            // Hook up events
            scene.EventManager.OnNewClient += OnNewClient;

            // Take ownership of the ISearchModule service
            scene.RegisterModuleInterface<ISearchModule>(this);

            // Add our scene to our list...
            lock(m_Scenes)
            {
                m_Scenes.Add(scene);
            }
        }

        public void RemoveRegion(Scene scene)
        {
            if (!m_Enabled)
                return;

            scene.UnregisterModuleInterface<ISearchModule>(this);

            scene.EventManager.OnNewClient -= OnNewClient;

            lock(m_Scenes)
            {
                m_Scenes.Remove(scene);
            }
        }

        public void RegionLoaded(Scene scene)
        {
        }

        public Type ReplaceableInterface
        {
            get { return null; }
        }

        public void PostInitialise()
        {
        }

        public void Close()
        {
        }

        public string Name
        {
            get { return "OpenSimSearch"; }
        }

        public static bool IsSharedModule
        {
            get { return true; }
        }
        #endregion

        /// New Client Event Handler
        private void OnNewClient(IClientAPI client)
        {
            // Subscribe to messages
            client.OnDirPlacesQuery += DirPlacesQuery;
            client.OnDirFindQuery += DirFindQuery;
            client.OnDirPopularQuery += DirPopularQuery;
            client.OnDirLandQuery += DirLandQuery;
            client.OnDirClassifiedQuery += DirClassifiedQuery;
            // Response after Directory Queries
            client.OnEventInfoRequest += EventInfoRequest;
            client.OnClassifiedInfoRequest += ClassifiedInfoRequest;
            client.OnMapItemRequest += HandleMapItemRequest;
        }

        //
        // Make external XMLRPC request
        //
        private Hashtable GenericXMLRPCRequest(Hashtable ReqParams, string method)
        {
            ArrayList SendParams = new()
            {
                ReqParams
            };

            // Send Request
            XmlRpcResponse Resp;
            try
            {
                XmlRpcRequest Req = new(method, SendParams);
                Resp = Req.Send(m_SearchServer, 30000);
            }
            catch (WebException ex)
            {
                m_log.ErrorFormat("[SEARCH]: Unable to connect to Search " +
                        "Server {0}.  Exception {1}", m_SearchServer, ex);

                Hashtable ErrorHash = new()
                {
                    ["success"] = false,
                    ["errorMessage"] = "Unable to search at this time. ",
                    ["errorURI"] = ""
                };

                return ErrorHash;
            }
            catch (SocketException ex)
            {
                m_log.ErrorFormat(
                        "[SEARCH]: Unable to connect to Search Server {0}. " +
                        "Exception {1}", m_SearchServer, ex);

                Hashtable ErrorHash = new()
                {
                    ["success"] = false,
                    ["errorMessage"] = "Unable to search at this time. ",
                    ["errorURI"] = ""
                };

                return ErrorHash;
            }
            catch (XmlException ex)
            {
                m_log.ErrorFormat(
                        "[SEARCH]: Unable to connect to Search Server {0}. " +
                        "Exception {1}", m_SearchServer, ex);

                Hashtable ErrorHash = new()
                {
                    ["success"] = false,
                    ["errorMessage"] = "Unable to search at this time. ",
                    ["errorURI"] = ""
                };

                return ErrorHash;
            }
            if (Resp.IsFault)
            {
                Hashtable ErrorHash = new()
                {
                    ["success"] = false,
                    ["errorMessage"] = "Unable to search at this time. ",
                    ["errorURI"] = ""
                };
                return ErrorHash;
            }
            Hashtable RespData = (Hashtable)Resp.Value;

            return RespData;
        }

        protected void DirPlacesQuery(IClientAPI remoteClient, UUID queryID,
                string queryText, int queryFlags, int category, string simName,
                int queryStart)
        {
            Hashtable ReqHash = new()
            {
                ["text"] = queryText,
                ["flags"] = queryFlags.ToString(),
                ["category"] = category.ToString(),
                ["sim_name"] = simName,
                ["query_start"] = queryStart.ToString()
            };

            Hashtable result = GenericXMLRPCRequest(ReqHash, "dir_places_query");

            if (!Convert.ToBoolean(result["success"]))
            {
                remoteClient.SendAgentAlertMessage(result["errorMessage"].ToString(), false);
                return;
            }

            ArrayList dataArray = (ArrayList)result["data"];

            int count = (dataArray.Count > 100) ? 101 : dataArray.Count;

            DirPlacesReplyData[] data = new DirPlacesReplyData[count];

            int i = 0;

            foreach (Object o in dataArray)
            {
                Hashtable d = (Hashtable)o;

                data[i] = new DirPlacesReplyData
                {
                    parcelID = new UUID(d["parcel_id"].ToString()),
                    name = d["name"].ToString(),
                    forSale = Convert.ToBoolean(d["for_sale"]),
                    auction = Convert.ToBoolean(d["auction"]),
                    dwell = Convert.ToSingle(d["dwell"])
                };

                if (++i >= count)
                    break;
            }

            remoteClient.SendDirPlacesReply(queryID, data);
        }

        public void DirPopularQuery(IClientAPI remoteClient, UUID queryID, uint queryFlags)
        {
            Hashtable ReqHash = new()
            {
                ["flags"] = queryFlags.ToString()
            };

            Hashtable result = GenericXMLRPCRequest(ReqHash, "dir_popular_query");

            if (!Convert.ToBoolean(result["success"]))
            {
                remoteClient.SendAgentAlertMessage(result["errorMessage"].ToString(), false);
                return;
            }

            ArrayList dataArray = (ArrayList)result["data"];

            int count = (dataArray.Count > 100) ? 101 : dataArray.Count;

            DirPopularReplyData[] data = new DirPopularReplyData[count];

            int i = 0;

            foreach (Object o in dataArray)
            {
                Hashtable d = (Hashtable)o;

                data[i] = new DirPopularReplyData
                {
                    parcelID = new UUID(d["parcel_id"].ToString()),
                    name = d["name"].ToString(),
                    dwell = Convert.ToSingle(d["dwell"])
                };

                if (++i >= count)
                    break;
            }

            remoteClient.SendDirPopularReply(queryID, data);
        }

        public void DirLandQuery(IClientAPI remoteClient, UUID queryID,
                uint queryFlags, uint searchType, int price, int area,
                int queryStart)
        {
            Hashtable ReqHash = new()
            {
                ["flags"] = queryFlags.ToString(),
                ["type"] = searchType.ToString(),
                ["price"] = price.ToString(),
                ["area"] = area.ToString(),
                ["query_start"] = queryStart.ToString()
            };

            Hashtable result = GenericXMLRPCRequest(ReqHash, "dir_land_query");

            if (!Convert.ToBoolean(result["success"]))
            {
                remoteClient.SendAgentAlertMessage(result["errorMessage"].ToString(), false);
                return;
            }

            ArrayList dataArray = (ArrayList)result["data"];
            int count = 0;

            /* Count entries in dataArray with valid region name to */
            /* prevent allocating data array with too many entries. */
            foreach (Object o in dataArray)
            {
                Hashtable d = (Hashtable)o;

                if (d["name"] is not null)
                    ++count;
            }

            count = (count > 100) ? 101 : count;

            DirLandReplyData[] data = new DirLandReplyData[count];

            int i = 0;

            foreach (Object o in dataArray)
            {
                Hashtable d = (Hashtable)o;

                if (d["name"] is null)
                    continue;

                data[i] = new DirLandReplyData
                {
                    parcelID = new UUID(d["parcel_id"].ToString()),
                    name = d["name"].ToString(),
                    auction = Convert.ToBoolean(d["auction"]),
                    forSale = Convert.ToBoolean(d["for_sale"]),
                    salePrice = Convert.ToInt32(d["sale_price"]),
                    actualArea = Convert.ToInt32(d["area"])
                };

                if (++i >= count)
                    break;
            }

            remoteClient.SendDirLandReply(queryID, data);
        }

        public void DirFindQuery(IClientAPI remoteClient, UUID queryID,
                string queryText, uint queryFlags, int queryStart)
        {
            if (((DirFindFlags)queryFlags & DirFindFlags.DateEvents) == DirFindFlags.DateEvents)
            {
                DirEventsQuery(remoteClient, queryID, queryText, queryFlags,
                        queryStart);
                return;
            }
        }

        public void DirEventsQuery(IClientAPI remoteClient, UUID queryID,
                string queryText, uint queryFlags, int queryStart)
        {
            Hashtable ReqHash = new()
            {
                ["text"] = queryText,
                ["flags"] = queryFlags.ToString(),
                ["query_start"] = queryStart.ToString()
            };

            Hashtable result = GenericXMLRPCRequest(ReqHash, "dir_events_query");

            if (!Convert.ToBoolean(result["success"]))
            {
                remoteClient.SendAgentAlertMessage(result["errorMessage"].ToString(), false);
                return;
            }

            ArrayList dataArray = (ArrayList)result["data"];

            int count = (dataArray.Count > 100) ? 101 : dataArray.Count;

            DirEventsReplyData[] data = new DirEventsReplyData[count];

            int i = 0;

            foreach (Object o in dataArray)
            {
                Hashtable d = (Hashtable)o;

                data[i] = new DirEventsReplyData
                {
                    ownerID = new UUID(d["owner_id"].ToString()),
                    name = d["name"].ToString(),
                    eventID = Convert.ToUInt32(d["event_id"]),
                    date = d["date"].ToString(),
                    unixTime = Convert.ToUInt32(d["unix_time"]),
                    eventFlags = Convert.ToUInt32(d["event_flags"])
                };

                if (++i >= count)
                    break;
            }

            remoteClient.SendDirEventsReply(queryID, data);
        }

        public void DirClassifiedQuery(IClientAPI remoteClient, UUID queryID,
                string queryText, uint queryFlags, uint category,
                int queryStart)
        {
            Hashtable ReqHash = new()
            {
                ["text"] = queryText,
                ["flags"] = queryFlags.ToString(),
                ["category"] = category.ToString(),
                ["query_start"] = queryStart.ToString()
            };

            Hashtable result = GenericXMLRPCRequest(ReqHash, "dir_classified_query");

            if (!Convert.ToBoolean(result["success"]))
            {
                remoteClient.SendAgentAlertMessage(result["errorMessage"].ToString(), false);
                return;
            }

            ArrayList dataArray = (ArrayList)result["data"];

            int count = (dataArray.Count > 100) ? 101 : dataArray.Count;

            DirClassifiedReplyData[] data = new DirClassifiedReplyData[count];

            int i = 0;

            foreach (Object o in dataArray)
            {
                Hashtable d = (Hashtable)o;

                data[i] = new DirClassifiedReplyData
                {
                    classifiedID = new UUID(d["classifiedid"].ToString()),
                    name = d["name"].ToString(),
                    classifiedFlags = Convert.ToByte(d["classifiedflags"]),
                    creationDate = Convert.ToUInt32(d["creation_date"]),
                    expirationDate = Convert.ToUInt32(d["expiration_date"]),
                    price = Convert.ToInt32(d["priceforlisting"])
                };

                if (++i >= count)
                    break;
            }

            remoteClient.SendDirClassifiedReply(queryID, data);
        }

        public void EventInfoRequest(IClientAPI remoteClient, uint queryEventID)
        {
            Hashtable ReqHash = new()
            {
                ["eventID"] = queryEventID.ToString()
            };

            Hashtable result = GenericXMLRPCRequest(ReqHash, "event_info_query");

            if (!Convert.ToBoolean(result["success"]))
            {
                remoteClient.SendAgentAlertMessage(result["errorMessage"].ToString(), false);
                return;
            }

            ArrayList dataArray = (ArrayList)result["data"];
            if (dataArray.Count == 0)
            {
                // something bad happened here, if we could return an
                // event after the search,
                // we should be able to find it here
                // TODO do some (more) sensible error-handling here
                remoteClient.SendAgentAlertMessage("Couldn't find this event.",
                        false);
                return;
            }

            Hashtable d = (Hashtable)dataArray[0];
            EventData data = new()
            {
                eventID = Convert.ToUInt32(d["event_id"]),
                creator = d["creator"].ToString(),
                name = d["name"].ToString(),
                category = d["category"].ToString(),
                description = d["description"].ToString(),
                date = d["date"].ToString(),
                dateUTC = Convert.ToUInt32(d["dateUTC"]),
                duration = Convert.ToUInt32(d["duration"]),
                cover = Convert.ToUInt32(d["covercharge"]),
                amount = Convert.ToUInt32(d["coveramount"]),
                simName = d["simname"].ToString()
            };
            data.globalPos = (Vector3.TryParse(d["globalposition"].ToString(), out data.globalPos)) ? data.globalPos : new();
            data.eventFlags = Convert.ToUInt32(d["eventflags"]);

            remoteClient.SendEventInfoReply(data);
        }

        public void ClassifiedInfoRequest(UUID queryClassifiedID, IClientAPI remoteClient)
        {
            Hashtable ReqHash = new()
            {
                ["classifiedID"] = queryClassifiedID.ToString()
            };

            Hashtable result = GenericXMLRPCRequest(ReqHash, "classifieds_info_query");

            if (!Convert.ToBoolean(result["success"]))
            {
                remoteClient.SendAgentAlertMessage(result["errorMessage"].ToString(), false);
                return;
            }

            //The viewer seems to issue an info request even when it is
            //creating a new classified which means the data hasn't been
            //saved to the database yet so there is no info to find.
            ArrayList dataArray = (ArrayList)result["data"];
            if (dataArray.Count == 0)
            {
                // Something bad happened here if we could not return an
                // event after the search. We should be able to find it here.
                // TODO do some (more) sensible error-handling here
//                remoteClient.SendAgentAlertMessage("Couldn't find data for classified ad.",
//                        false);
                return;
            }

            Hashtable d = (Hashtable)dataArray[0];

            Vector3 globalPos = (Vector3.TryParse(d["posglobal"].ToString(), out globalPos)) ? globalPos : new();

            remoteClient.SendClassifiedInfoReply(
                    new UUID(d["classifieduuid"].ToString()),
                    new UUID(d["creatoruuid"].ToString()),
                    Convert.ToUInt32(d["creationdate"]),
                    Convert.ToUInt32(d["expirationdate"]),
                    Convert.ToUInt32(d["category"]),
                    d["name"].ToString(),
                    d["description"].ToString(),
                    new UUID(d["parceluuid"].ToString()),
                    Convert.ToUInt32(d["parentestate"]),
                    new UUID(d["snapshotuuid"].ToString()),
                    d["simname"].ToString(),
                    globalPos,
                    d["parcelname"].ToString(),
                    Convert.ToByte(d["classifiedflags"]),
                    Convert.ToInt32(d["priceforlisting"]));
        }

        public void HandleMapItemRequest(IClientAPI remoteClient, uint flags,
                                         uint EstateID, bool godlike,
                                         uint itemtype, ulong regionhandle)
        {
            //The following constant appears to be from GridLayerType enum
            //defined in OpenMetaverse/GridManager.cs of libopenmetaverse.
            if (itemtype == (uint)OpenMetaverse.GridItemType.LandForSale)
            {
                Hashtable ReqHash = new()
                {
                    //The flags are: SortAsc (1 << 15), PerMeterSort (1 << 17)
                    ["flags"] = "163840",
                    ["type"] = "4294967295", //This is -1 in 32 bits
                    ["price"] = "0",
                    ["area"] = "0",
                    ["query_start"] = "0"
                };

                Hashtable result = GenericXMLRPCRequest(ReqHash, "dir_land_query");

                if (!Convert.ToBoolean(result["success"]))
                {
                    remoteClient.SendAgentAlertMessage(result["errorMessage"].ToString(), false);
                    return;
                }

                ArrayList dataArray = (ArrayList)result["data"];

                List<mapItemReply> mapitems = new();
                string ParcelRegionUUID;
                string[] landingpoint;

                foreach (Object o in dataArray)
                {
                    Hashtable d = (Hashtable)o;

                    if (d["name"] is null)
                        continue;

                    mapItemReply mapitem = new();

                    ParcelRegionUUID = d["region_UUID"].ToString();

                    foreach (Scene scene in m_Scenes)
                    {
                        if (scene.RegionInfo.RegionID.ToString() == ParcelRegionUUID)
                        {
                            landingpoint = d["landing_point"].ToString().Split('/');

                            mapitem.x = (uint)((scene.RegionInfo.RegionLocX * 256) +
                                                Convert.ToDecimal(landingpoint[0]));
                            mapitem.y = (uint)((scene.RegionInfo.RegionLocY * 256) +
                                                Convert.ToDecimal(landingpoint[1]));
                            break;
                        }
                    }

                    mapitem.id = new UUID(d["parcel_id"].ToString());
                    mapitem.Extra = Convert.ToInt32(d["area"]);
                    mapitem.Extra2 = Convert.ToInt32(d["sale_price"]);
                    mapitem.name = d["name"].ToString();

                    mapitems.Add(mapitem);
                }

                remoteClient.SendMapItemReply(mapitems.ToArray(), itemtype, flags);
                mapitems.Clear();
            }

            if (itemtype == (uint)OpenMetaverse.GridItemType.PgEvent ||
                itemtype == (uint)OpenMetaverse.GridItemType.MatureEvent ||
                itemtype == (uint)OpenMetaverse.GridItemType.AdultEvent)
            {

                //Find the maturity level
                int maturity = (1 << 24);

                //Find the maturity level
                if (itemtype == (uint)OpenMetaverse.GridItemType.MatureEvent)
                    maturity = (1 << 25);
                else
                {
                    if (itemtype == (uint)OpenMetaverse.GridItemType.AdultEvent)
                        maturity = (1 << 26);
                }

                //The flags are: SortAsc (1 << 15), PerMeterSort (1 << 17)
                maturity |= 163840;

                //When character before | is a u get upcoming/in-progress events
                //Character before | is number of days before/after current date
                //Characters after | is the number for a category
                Hashtable ReqHash = new()
                {
                    ["text"] = "u|0",
                    ["flags"] = maturity.ToString(),
                    ["query_start"] = "0"
                };

                Hashtable result = GenericXMLRPCRequest(ReqHash, "dir_events_query");

                if (!Convert.ToBoolean(result["success"]))
                {
                    remoteClient.SendAgentAlertMessage(result["errorMessage"].ToString(), false);
                    return;
                }

                ArrayList dataArray = (ArrayList)result["data"];

                List<mapItemReply> mapitems = new();
                int event_id;
                string[] landingpoint;

                foreach (Object o in dataArray)
                {
                    Hashtable d = (Hashtable)o;

                    if (d["name"] is null)
                        continue;

                    mapItemReply mapitem = new();

                    //Events use a comma separator in the landing point
                    landingpoint = d["landing_point"].ToString().Split(',');
                    mapitem.x = Convert.ToUInt32(landingpoint[0]);
                    mapitem.y = Convert.ToUInt32(landingpoint[1]);

                    //This is a crazy way to pass the event ID back to the
                    //viewer but that is the way it wants the information.
                    event_id = Convert.ToInt32(d["event_id"]);
                    mapitem.id = new UUID("00000000-0000-0000-0000-0000" +
                                            event_id.ToString("X8"));

                    mapitem.Extra = Convert.ToInt32(d["unix_time"]);
                    mapitem.Extra2 = 0; //FIXME: No idea what to do here
                    mapitem.name = d["name"].ToString();

                    mapitems.Add(mapitem);
                }

                remoteClient.SendMapItemReply(mapitems.ToArray(), itemtype, flags);
                mapitems.Clear();
            }
        }

        public void Refresh()
        {
        }
    }
}
